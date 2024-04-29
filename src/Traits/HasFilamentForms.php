<?php

namespace Tapp\FilamentForms\Traits;

use Tapp\FilamentForms\Models\FilamentForm;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasFilamentForms
{
    public function FilamentForms(): BelongsToMany
    {
        return $this->belongsToMany(FilamentForm::class);
    }
}
