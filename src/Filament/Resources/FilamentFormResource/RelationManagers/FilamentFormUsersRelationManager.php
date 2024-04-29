<?php

namespace Tapp\FilamentForms\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\BulkAction;
use Tapp\FilamentForms\Exports\FilamentFormUsersExport;
use Illuminate\Database\Eloquent\Collection;
use Filament\Resources\RelationManagers\RelationManager;

class FilamentFormUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormUsers';

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
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordUrl(fn ($record) => route(config('filament-forms.entry-show-route'), $record))
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
                        ->action(fn (Collection $records) =>
                                    Excel::download(
                                        new FilamentFormUsersExport($records),
                                        $this->getOwnerRecord()->name.'_form_entry_export'.now()->format('Y-m-dhis').'.csv')
                                )
                        ->icon('heroicon-o-document-chart-bar')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}