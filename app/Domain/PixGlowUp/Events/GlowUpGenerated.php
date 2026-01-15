<?php

namespace App\Domain\PixGlowUp\Events;

use App\Models\GlowupJob;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GlowUpGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public GlowupJob $glowUpJob)
    {

    }
}
