<?php

namespace Tapp\FilamentForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentForms\Models\FilamentFormUser;
use App\Models\User;

class FilamentForm extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
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
}