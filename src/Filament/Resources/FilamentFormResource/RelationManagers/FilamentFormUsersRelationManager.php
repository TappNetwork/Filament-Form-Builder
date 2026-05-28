<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Tapp\FilamentFormBuilder\Exports\FilamentFormUsersExport;
use Tapp\FilamentFormBuilder\Filament\Infolists\FilamentFormUserEntryInfolist;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;
use Tapp\FilamentFormBuilder\Support\FormEntryUrl;

class FilamentFormUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormUsers';

    /**
     * Apps may restrict visibility of the Entries relation manager per form by defining
     * a viewEntries($user, $form) method on the FilamentForm (owner) policy. When present,
     * that policy is used; otherwise the default (related model viewAny) applies.
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! parent::canViewForRecord($ownerRecord, $pageClass)) {
            return false;
        }

        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return self::userCanViewEntriesForOwner($user, $ownerRecord);
    }

    /**
     * Whether the given user can view/export entries for the given owner form.
     * Uses the owner model's viewEntries policy when present.
     */
    protected static function userCanViewEntriesForOwner(Authenticatable&Authorizable $user, Model $owner): bool
    {
        $policy = policy($owner);
        if ($policy && method_exists($policy, 'viewEntries')) {
            return $user->can('viewEntries', $owner);
        }

        return true;
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(config('filament-form-builder.admin-panel-filament-form-user-name-plural'));
    }

    public static function getLabel(): string
    {
        return __(config('filament-form-builder.admin-panel-filament-form-user-name'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('filamentFormUser.user.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return FilamentFormUserEntryInfolist::configure($schema, dense: true);
    }

    public function table(Table $table): Table
    {
        $usesEntrySlideover = $this->usesAdminEntrySlideover();

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['user', 'filamentForm']))
            ->recordTitleAttribute('user.name')
            ->heading(config('filament-form-builder.admin-panel-filament-form-user-name-plural'))
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordUrl($usesEntrySlideover ? null : fn (FilamentFormUser $record): string => FormEntryUrl::show($record))
            ->recordAction($usesEntrySlideover ? ViewAction::getDefaultName() : null)
            ->filters([
                Filter::make('guest_entries')
                    ->query(fn (Builder $query): Builder => $query->whereNull('user_id')),
                Filter::make('user_entries')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_id')),
            ])
            ->headerActions([
            ])
            ->recordActions([
                ActionGroup::make([
                    $this->viewEntryAction(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('Export Selected')
                        ->action(fn (Collection $records) => Excel::download(
                            new FilamentFormUsersExport($records),
                            urlencode($this->getOwnerRecord()->name).'_form_entry_export'.now()->format('Y-m-dhis').'.csv')
                        )
                        ->icon('heroicon-o-document-chart-bar')
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn (): bool => $this->canViewEntriesForOwner()),
                ]),
            ]);
    }

    protected function usesAdminEntrySlideover(): bool
    {
        if (config('filament-form-builder.admin-panel-entry-display', 'slideover') !== 'slideover') {
            return false;
        }

        $adminPanelId = config('filament-form-builder.admin-panel-id');

        if (! is_string($adminPanelId) || $adminPanelId === '') {
            return false;
        }

        return Filament::getCurrentPanel()?->getId() === $adminPanelId;
    }

    protected function viewEntryAction(): ViewAction
    {
        return ViewAction::make()
            ->slideOver()
            ->modalWidth(Width::TwoExtraLarge)
            ->modalHeading(__('Form Submission'))
            ->visible(fn (): bool => $this->usesAdminEntrySlideover());
    }

    protected function getViewAuthorizationResponse(Model $record): Response
    {
        if (! $record instanceof FilamentFormUser) {
            return parent::getViewAuthorizationResponse($record);
        }

        if ($record->user_id === null) {
            return Response::allow();
        }

        $policy = policy($record);

        if ($policy && method_exists($policy, 'view')) {
            return parent::getViewAuthorizationResponse($record);
        }

        $user = Auth::user();

        if (! $user) {
            return Response::deny();
        }

        if ($record->user_id === $user->getAuthIdentifier()) {
            return Response::allow();
        }

        return Response::deny();
    }

    /**
     * Whether the current user can view/export entries for the owner form.
     * Uses the owner model's viewEntries policy when present.
     */
    protected function canViewEntriesForOwner(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        /** @var Authenticatable&Authorizable $user */
        return self::userCanViewEntriesForOwner($user, $this->getOwnerRecord());
    }
}
