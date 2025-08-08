<?php

namespace Tapp\FilamentFormBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;

/**
 * @property int $id
 * @property string $label
 * @property FilamentFieldTypeEnum $type
 * @property array|null $options
 * @property array|null $rules
 * @property int $order
 * @property-read FilamentForm $filamentForm
 */
class FilamentFormField extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => false,
    ];

    protected $guarded = [];

    protected $casts = [
        'type' => FilamentFieldTypeEnum::class,
        'options' => 'array',
        'rules' => 'array',
        'schema' => 'array',
    ];

    public function filamentForm(): BelongsTo
    {
        return $this->belongsTo(FilamentForm::class);
    }
}
