<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormField;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\EditFilamentForm;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\ListFilamentForms;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\CreateFilamentForm;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormUsersRelationManager;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormFieldsRelationManager;

class FilamentFormResource extends Resource
{
    protected static ?string $model = FilamentForm::class;

    protected static ?int $navigationSort = 99;

    public static function getBreadcrumb(): string
    {
        return config('filament-form-builder.admin-panel-resource-name-plural');
    }

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
                Toggle::make('permit_guest_entries')
                    ->hint('Permit non registered users to submit this form'),
                Forms\Components\TextInput::make('redirect_url')
                    ->hint('(optional) complete this field to provide a custom redirect url on form completion. Use a fully qualified URL including "https://" to redirect to an external link, otherwise url will be relative to this sites domain'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('form_link')
                    ->copyable()
                    ->copyMessage('Form link copied to clipboard')
                    ->copyMessageDuration(1500),
                IconColumn::make('permit_guest_entries')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return (bool) $record->permit_guest_entries;
                    })
                    ->boolean(),
                IconColumn::make('locked')
                    ->sortable()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('copy')
                    ->action(function ($record) {
                        $formCopy = FilamentForm::create([
                            'name' => $record->name.' - (Copy)',
                            'permit_guest_entries' => $record->permit_guest_entries,
                            'redirect_url' => $record->redirect_url,
                            'description' => $record->description,
                        ]);

                        $record->filamentFormFields->each(function ($field) use ($formCopy) {
                            FilamentFormField::create([
                                'filament_form_id' => $formCopy->id,
                                'label' => $field->label,
                                'name' => $field->name,
                                'type' => $field->type,
                                'required' => $field->required,
                                'order' => $field->order,
                                'hint' => $field->hint,
                                'options' => $field->options,
                                'rules' => $field->rules,
                            ]);
                        });

                        Notification::make()
                            ->title('Form copied successfully')
                            ->body('Please change the name of the form to something unique and remove the "(Copy)" suffix')
                            ->success()
                            ->send();

                        return Redirect::to('/admin/filament-forms/'.$formCopy->id.'/edit');
                    })
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No '.config('filament-form-builder.admin-panel-resource-name-plural'));
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
