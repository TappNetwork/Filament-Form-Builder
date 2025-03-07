<?php

namespace Tapp\FilamentFormBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string|null $redirect_url
 * @property bool $permit_guest_entries
 * @property-read string $form_link
 */
class FilamentForm extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'permit_guest_entries' => 'boolean',
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
