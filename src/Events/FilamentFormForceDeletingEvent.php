<?php

namespace Tapp\FilamentFormBuilder\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

class FilamentFormForceDeletingEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public FilamentForm $form) {}
}
