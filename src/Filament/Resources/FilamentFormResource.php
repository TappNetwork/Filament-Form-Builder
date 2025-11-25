<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Redirect;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\CreateFilamentForm;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\EditFilamentForm;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages\ListFilamentForms;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormFieldsRelationManager;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers\FilamentFormUsersRelationManager;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormField;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Toggle::make('permit_guest_entries')
                    ->hint('Permit non registered users to submit this form'),
                TextInput::make('redirect_url')
                    ->hint('(optional) complete this field to provide a custom redirect url on form completion. Use a fully qualified URL including "https://" to redirect to an external link, otherwise url will be relative to this sites domain'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Section::make('Notifications')
                    ->description('Configure email notifications for form submissions')
                    ->schema([
                        static::getNotificationEmailsField(),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
            ->recordActions([
                EditAction::make(),
                Action::make('preview')
                    ->visible(fn () => (bool) config('filament-form-builder.preview-route'))
                    ->url(fn ($record) => route(config('filament-form-builder.preview-route'), ['form' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('copy')
                    ->action(function ($record) {
                        $formCopy = FilamentForm::create([
                            'name' => $record->name.' - (Copy)',
                            'permit_guest_entries' => $record->permit_guest_entries,
                            'redirect_url' => $record->redirect_url,
                            'description' => $record->description,
                            'notification_emails' => $record->notification_emails,
                        ]);

                        $record->filamentFormFields->each(function ($field) use ($formCopy) {
                            FilamentFormField::create([
                                'filament_form_id' => $formCopy->id,
                                'label' => $field->label,
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
                    }),
            ])
            ->toolbarActions([
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

    protected static function getNotificationEmailsField(): Component
    {
        $userModel = config('filament-form-builder.user_model');

        // If user model is configured, use Select with user search
        if ($userModel && class_exists($userModel)) {
            return Select::make('notification_emails')
                ->label('Notification Recipients')
                ->helperText('Select users who should receive notifications when this form is submitted.')
                ->multiple()
                ->searchable()
                ->getSearchResultsUsing(function ($search) use ($userModel) {
                    return $userModel::query()
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($user) => [$user->email => $user->name.' ('.$user->email.')']);
                })
                ->getOptionLabelsUsing(function (array $values) use ($userModel): array {
                    return $userModel::whereIn('email', $values)
                        ->get()
                        ->mapWithKeys(fn ($user) => [$user->email => $user->name.' ('.$user->email.')'])
                        ->toArray();
                });
        }

        // Default: TagsInput for manual email entry
        return TagsInput::make('notification_emails')
            ->label('Notification Email Addresses')
            ->helperText('Enter email addresses that should receive notifications when this form is submitted. Press Enter after each email.')
            ->placeholder('email@example.com');
    }
}
