<?php

namespace Tapp\FilamentFormBuilder\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
