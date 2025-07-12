<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;

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
            ->components([
                Select::make('type')
                    ->options(function () {
                        return collect(FilamentFieldTypeEnum::cases())
                            ->mapWithKeys(fn ($type) => [$type->name => $type->fieldName()])
                            ->sortBy(fn ($label, $key) => $label)
                            ->toArray();
                    })
                    ->required()
                    ->live(),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? 'Heading' : 'Label';
                    }),
                TagsInput::make('options')
                    ->placeholder('Add options')
                    ->hint('Press enter after inputting each option')
                    ->visible(function (Get $get) {
                        if ($get('type')) {
                            return FilamentFieldTypeEnum::fromString($get('type'))->hasOptions();
                        }

                        return false;
                    }),
                Textarea::make('hint')
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? 'Subheading' : 'Hint';
                    }),
                TagsInput::make('rules')
                    ->placeholder('Add rules')
                    ->hint('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules')
                    ->visible(function (Get $get) {
                        return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                            && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                    }),
                TextInput::make('order')
                    ->default(function () {
                        return $this->getOwnerRecord()->filamentFormFields()->count() + 1;
                    })
                    ->numeric(),
                Toggle::make('required')
                    ->visible(function (Get $get) {
                        return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                            && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                    }),
                Repeater::make('schema')
                    ->label('Fields')
                    ->schema([
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->options(function () {
                                $options = collect(FilamentFieldTypeEnum::cases())
                                    ->filter(fn ($type) => $type !== FilamentFieldTypeEnum::REPEATER)
                                    ->mapWithKeys(fn ($type) => [$type->name => $type->fieldName()])
                                    ->toArray();

                                return $options;
                            })
                            ->required()
                            ->live(),
                        TagsInput::make('options')
                            ->placeholder('Add options')
                            ->hint('Press enter after inputting each option')
                            ->visible(function (Get $get) {
                                if ($get('type')) {
                                    return FilamentFieldTypeEnum::fromString($get('type'))->hasOptions();
                                }

                                return false;
                            }),
                        Textarea::make('hint'),
                        TagsInput::make('rules')
                            ->placeholder('Add rules')
                            ->hint('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules'),
                        Toggle::make('required'),
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
                TextColumn::make('label'),
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
                EditAction::make()
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    }),
                DeleteAction::make()
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function () use ($form) {
                            return ! $form->locked;
                        }),
                ]),
            ]);
    }
}
