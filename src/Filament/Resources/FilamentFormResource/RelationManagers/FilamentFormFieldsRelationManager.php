<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;
use Tapp\FilamentFormBuilder\Support\OptionsEditor;

class FilamentFormFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormFields';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(config('filament-form-builder.admin-panel-filament-form-field-name-plural'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Grid::make(3)
                    ->schema([
                        Select::make('type')
                            ->options(function () {
                                return collect(FilamentFieldTypeEnum::cases())
                                    ->mapWithKeys(fn ($type) => [$type->name => $type->fieldName()])
                                    ->sortBy(fn ($label, $key) => $label)
                                    ->toArray();
                            })
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        TextInput::make('order')
                            ->default(function () {
                                return $this->getOwnerRecord()->filamentFormFields()->count() + 1;
                            })
                            ->numeric()
                            ->columnSpan(1),
                        Toggle::make('required')
                            ->inline(false)
                            ->visible(function (Get $get) {
                                return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                                    && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                            })
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
                Textarea::make('label')
                    ->required()
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? 'Heading' : 'Label';
                    })
                    ->columnSpanFull(),
                self::optionsEditor(),
                Textarea::make('hint')
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? 'Subheading' : 'Hint';
                    })
                    ->columnSpanFull(),
                // TagsInput::make('rules')
                //     ->placeholder('Add rules')
                //     ->hint('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules')
                //     ->visible(function (Get $get) {
                //         return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                //             && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                //     }),
                Repeater::make('schema')
                    ->label('Fields')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->options(function () {
                                        $options = collect(FilamentFieldTypeEnum::cases())
                                            ->filter(fn ($type) => $type !== FilamentFieldTypeEnum::REPEATER)
                                            ->mapWithKeys(fn ($type) => [$type->name => $type->fieldName()])
                                            ->toArray();

                                        return $options;
                                    })
                                    ->required()
                                    ->live()
                                    ->columnSpan(1),
                                Toggle::make('required')
                                    ->inline(false)
                                    ->columnSpan(1),
                            ])
                            ->columnSpanFull(),
                        Textarea::make('label')
                            ->required()
                            ->columnSpanFull(),
                        self::optionsEditor(),
                        Textarea::make('hint')
                            ->columnSpanFull(),
                        // TagsInput::make('rules')
                        //     ->placeholder('Add rules')
                        //     ->hint('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules'),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::REPEATER->name;
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        $form = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('label')
            ->heading(config('filament-form-builder.admin-panel-filament-form-field-name-plural'))
            ->modelLabel(config('filament-form-builder.admin-panel-filament-form-field-name'))
            ->reorderable('order')
            ->columns([
                TextColumn::make('label')
                    ->limit(60)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (! is_string($state) || strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->formatStateUsing(function ($record) {
                        return $record->type->fieldName();
                    }),
                IconColumn::make('required')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return (bool) $record->required;
                    })
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    })
                    ->label('Create Field'),
                Action::make('lock_fields')
                    ->requiresConfirmation()
                    ->modalHeading('Lock Form Fields. Doing this will lock the forms fields and new fields will no longer be able to be changed or edited')
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    })
                    ->action(function () use ($form) {
                        $form->update([
                            'locked' => true,
                        ]);
                    }),
                Action::make('unlock_fields')
                    ->requiresConfirmation()
                    ->modalHeading('Unlock Form Fields. Changing fields after entries has been made can cause inconsistencies for prexisting entries')
                    ->visible(function () use ($form) {
                        return $form->locked;
                    })
                    ->action(function () use ($form) {
                        $form->update([
                            'locked' => false,
                        ]);
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(function () use ($form) {
                            return ! $form->locked;
                        }),
                    DeleteAction::make()
                        ->visible(function () use ($form) {
                            return ! $form->locked;
                        }),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function () use ($form) {
                            return ! $form->locked;
                        }),
                ]),
            ]);
    }

    private static function optionsEditor(): Grid
    {
        return Grid::make(1)
            ->schema(fn (Get $get): array => OptionsEditor::components($get('type')))
            ->columnSpanFull();
    }
}
