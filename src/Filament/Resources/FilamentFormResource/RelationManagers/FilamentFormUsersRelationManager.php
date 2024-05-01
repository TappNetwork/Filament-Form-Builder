<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Tapp\FilamentFormBuilder\Exports\FilamentFormUsersExport;

class FilamentFormUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormUsers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(config('filament-forms.admin-panel-filament-form-user-name-plural'));
    }

    public static function getLabel(): string
    {
        return 'Custom Posts Title';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('filamentFormUser.user.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->heading(config('filament-forms.admin-panel-filament-form-user-name-plural'))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordUrl(fn ($record) => route(config('filament-forms.filament-form-user-show-route'), $record))
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('Export Selected')
                        ->action(fn (Collection $records) => Excel::download(
                            new FilamentFormUsersExport($records),
                            $this->getOwnerRecord()->name.'_form_entry_export'.now()->format('Y-m-dhis').'.csv')
                        )
                        ->icon('heroicon-o-document-chart-bar')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
