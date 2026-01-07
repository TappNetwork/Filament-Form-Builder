<?php

namespace Tapp\FilamentFormBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tapp\FilamentFormBuilder\Models\Traits\BelongsToTenant;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $redirect_url
 * @property bool $permit_guest_entries
 * @property array<int, string>|null $notification_emails
 * @property-read string $form_link
 */
class FilamentForm extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'permit_guest_entries' => 'boolean',
        'notification_emails' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model', Authenticatable::class));
    }

    public function filamentFormFields(): HasMany
    {
        return $this->hasMany(FilamentFormField::class)
            ->orderBy('order', 'asc');
    }

    public function filamentFormUsers(): HasMany
    {
        return $this->hasMany(FilamentFormUser::class);
    }

    public function getFormLinkAttribute(): string
    {
        return route(config('filament-form-builder.filament-form-show-route'), $this->id);
    }
}
