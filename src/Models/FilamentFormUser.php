<?php

namespace Tapp\FilamentForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tapp\FilamentForms\Models\FilamentForm;
use App\Models\User;

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
