<?php

namespace Tapp\FilamentForms\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Tapp\FilamentForms\Models\FilamentForm;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource\Pages\EditFilamentForm;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource\Pages\ListFilamentForms;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource\Pages\CreateFilamentForm;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormUsersRelationManager;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormFieldsRelationManager;

class FilamentFormResource extends Resource
{
    protected static ?string $model = FilamentForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Forms';

    protected static ?string $navigationLabel = 'Forms';

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
