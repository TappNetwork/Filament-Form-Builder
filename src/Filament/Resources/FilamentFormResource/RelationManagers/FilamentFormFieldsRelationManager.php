<?php

namespace Tapp\FilamentForms\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Tapp\FilamentForms\Enums\FilamentFieldTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class FilamentFormFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormFields';

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
