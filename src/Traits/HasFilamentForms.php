<?php

namespace Tapp\FilamentFormBuilder\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

/** @phpstan-ignore-next-line */
trait HasFilamentForms
{
    public function FilamentForms(): BelongsToMany
    {
        return $this->belongsToMany(FilamentForm::class);
    }
}
