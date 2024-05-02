<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\CreateFilamentForm;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\EditFilamentForm;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\ListFilamentForms;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormFieldsRelationManager;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormUsersRelationManager;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

class FilamentFormResource extends Resource
{
    protected static ?string $model = FilamentForm::class;

    protected static ?int $navigationSort = 99;

    public static function getNavigationGroup(): ?string
    {
        return config('filament-form-builder.admin-panel-group-name');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-form-builder.admin-panel-icon');
    }

    public static function getNavigationLabel(): string
    {
        return config('filament-form-builder.admin-panel-resource-name-plural');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-form-builder.admin-panel-sort-order');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FilamentFormFieldsRelationManager::class,
            FilamentFormUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFilamentForms::route('/'),
            'create' => CreateFilamentForm::route('/create'),
            'edit' => EditFilamentForm::route('/{record}/edit'),
        ];
    }
}
