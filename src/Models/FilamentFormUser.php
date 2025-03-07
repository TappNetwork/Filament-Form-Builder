<?php

namespace Tapp\FilamentFormBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property array $entry
 * @property array|null $firstEntry
 * @property-read array $key_value_entry
 */
class FilamentFormUser extends Model
{
    use HasFactory;

    protected $table = 'filament_form_user';

    protected $guarded = [];

    protected $casts = [
        'entry' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class));
    }

    public function filamentForm(): BelongsTo
    {
        return $this->belongsTo(FilamentForm::class);
    }

    public function getKeyValueEntryAttribute()
    {
        $keyValueEntry = [];

        foreach ($this->entry as $fieldEntry) {
            $keyValueEntry[$fieldEntry['field']] = $fieldEntry['answer'];
        }

        return $keyValueEntry;
    }
}
