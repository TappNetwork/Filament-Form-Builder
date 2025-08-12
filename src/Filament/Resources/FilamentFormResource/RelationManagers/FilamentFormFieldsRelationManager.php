<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('step')
                    ->label(__('Wizard Step'))
                    ->options(function () {
                        $owner = $this->getOwnerRecord();
                        if (! $owner || ! $owner->is_wizard) {
                            return [];
                        }
                        $steps = $owner->getOrderedWizardSteps();
                        $options = [];
                        foreach ($steps as $index => $step) {
                            $title = is_array($step) ? ($step['title'] ?? ('Step '.($index + 1))) : (string) $step;
                            $options[$index] = $title;
                        }

                        return $options;
                    })
                    ->visible(function () {
                        $owner = $this->getOwnerRecord();

                        return (bool) ($owner && $owner->is_wizard);
                    })
                    ->required(function () {
                        $owner = $this->getOwnerRecord();

                        return (bool) ($owner && $owner->is_wizard);
                    }),
                Select::make('type')
                    ->label(__('Type'))
                    ->options(function () {
                        return collect(FilamentFieldTypeEnum::cases())
                            ->mapWithKeys(fn ($type) => [$type->name => $type->fieldName()])
                            ->sortBy(fn ($label, $key) => $label)
                            ->toArray();
                    })
                    ->required()
                    ->live(),
                TextInput::make('label')
                    ->label(__('Label'))
                    ->required()
                    ->maxLength(255)
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? __('Heading') : __('Label');
                    }),
                TagsInput::make('options')
                    ->label(__('Options'))
                    ->placeholder(__('Add options'))
                    ->hint(__('Press enter after inputting each option'))
                    ->visible(function (Get $get) {
                        if ($get('type')) {
                            return FilamentFieldTypeEnum::fromString($get('type'))->hasOptions();
                        }

                        return false;
                    }),
                Textarea::make('hint')
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? __('Subheading') : __('Hint');
                    }),
                TagsInput::make('rules')
                    ->label(__('Rules'))
                    ->placeholder(__('Add rules'))
                    ->hint(__('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules'))
                    ->visible(function (Get $get) {
                        return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                            && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                    }),
                TextInput::make('order')
                    ->label(__('Order'))
                    ->default(function () {
                        return $this->getOwnerRecord()->filamentFormFields()->count() + 1;
                    })
                    ->numeric(),
                Toggle::make('required')
                    ->label(__('Required'))
                    ->visible(function (Get $get) {
                        return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                            && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                    }),
                Repeater::make('schema')
                    ->label(__('Fields'))
                    ->schema([
                        TextInput::make('label')
                            ->label(__('Label'))
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label(__('Type'))
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
                            ->label(__('Options'))
                            ->placeholder(__('Add options'))
                            ->hint(__('Press enter after inputting each option'))
                            ->visible(function (Get $get) {
                                if ($get('type')) {
                                    return FilamentFieldTypeEnum::fromString($get('type'))->hasOptions();
                                }

                                return false;
                            }),
                        Textarea::make('hint')->label(__('Hint')),
                        TagsInput::make('rules')
                            ->label(__('Rules'))
                            ->placeholder(__('Add rules'))
                            ->hint(__('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules')),
                        Toggle::make('required')->label(__('Required')),
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
            ->heading(__(config('filament-form-builder.admin-panel-filament-form-field-name-plural')))
            ->modelLabel(__(config('filament-form-builder.admin-panel-filament-form-field-name')))
            ->reorderable('order')
            ->columns([
                TextColumn::make('label')->label(__('Label')),
                TextColumn::make('order')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->formatStateUsing(function ($record) {
                        return $record->type->fieldName();
                    }),
                IconColumn::make('required')
                    ->label(__('Required'))
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return (bool) $record->required;
                    })
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    })
                    ->label(__('Create Field')),
                Action::make('lock_fields')
                    ->label(__('Lock fields'))
                    ->requiresConfirmation()
                    ->modalHeading(__('Lock Form Fields. Doing this will lock the forms fields and new fields will no longer be able to be changed or edited.'))
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    })
                    ->action(function () use ($form) {
                        $form->update([
                            'locked' => true,
                        ]);
                    }),
                Action::make('unlock_fields')
                    ->label(__('Unlock fields'))
                    ->requiresConfirmation()
                    ->modalHeading(__('Unlock Form Fields. Changing fields after entries has been made can cause inconsistencies for preexisting entries.'))
                    ->visible(function () use ($form) {
                        return $form->locked;
                    })
                    ->action(function () use ($form) {
                        $form->update([
                            'locked' => false,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function () use ($form) {
                        return ! $form->locked;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function () use ($form) {
                            return ! $form->locked;
                        }),
                ]),
            ]);
    }
}
