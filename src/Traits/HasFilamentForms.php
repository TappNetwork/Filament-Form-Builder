<?php

namespace Tapp\FilamentForms\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentForms\Models\FilamentForm;

trait HasFilamentForms
{
    public function FilamentForms(): BelongsToMany
    {
        return $this->belongsToMany(FilamentForm::class);
    }
}
