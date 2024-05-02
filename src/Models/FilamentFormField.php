<?php

namespace Tapp\FilamentFormBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class FilamentFormField extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];


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
