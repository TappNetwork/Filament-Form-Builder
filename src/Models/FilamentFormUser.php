<?php

namespace Tapp\FilamentFormBuilder\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(User::class);
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
