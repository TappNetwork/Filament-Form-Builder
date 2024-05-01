<?php

namespace Tapp\FilamentFormBuilder\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

trait HasFilamentForms
{
    public function FilamentForms(): BelongsToMany
    {
        return $this->belongsToMany(FilamentForm::class);
    }
}
