<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
        return __(config('filament-forms.admin-panel-filament-form-field-name-plural'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options(FilamentFieldTypeEnum::class)
                    ->live(),
                TagsInput::make('options')
                    ->hint('Press enter after inputting each option')
                    ->visible(function (Get $get) {
                        if ($get('type')) {
                            return FilamentFieldTypeEnum::fromString($get('type'))->hasOptions();
                        }

                        return false;
                    }),
                TextInput::make('hint'),
                TagsInput::make('rules')
                    ->hint('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules'),
                TextInput::make('order')
                    ->default(function () {
                        return $this->getOwnerRecord()->filamentFormFields()->count() + 1;
                    })
                    ->numeric(),
                Toggle::make('required'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->heading(config('filament-forms.admin-panel-filament-form-field-name-plural'))
            ->columns([
                TextColumn::make('label'),
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
