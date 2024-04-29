<?php

namespace Tapp\FilamentForms\Models;

use Tapp\FilamentForms\Enums\FilamentFieldTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FilamentFormField extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => FilamentFieldTypeEnum::class,
        'options' => 'array',
        'rules' => 'array',
    ];

    public function filamentForm(): BelongsTo
    {
        return $this->belongsTo(FilamentForm::class);
    }
}
