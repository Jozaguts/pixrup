<?php

namespace App\Events;

use App\Http\Resources\GlowUp\GlowUpJobResource;
use App\Models\GlowupJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GlowUpJobUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public GlowupJob $job,
    ) {
        $this->job->refresh();
    }

    public function broadcastOn(): Channel
    {
        $channel = config('glowup.broadcast_channel_prefix','glowup.jobs.');

        return  new PrivateChannel($channel.$this->job->property_id); // private channel adds private prefix so, channel name is private-glowup.jobs.X
    }

    public function broadcastAs(): string
    {
        return 'GlowUpJobUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'job' => (new GlowUpJobResource($this->job))->resolve(),
        ];
    }
}
