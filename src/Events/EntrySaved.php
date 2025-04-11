<?php

namespace Tapp\FilamentFormBuilder\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

class EntrySaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public FilamentFormUser $entry
    ) {}
}
