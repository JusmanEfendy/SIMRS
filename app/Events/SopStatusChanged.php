<?php

namespace App\Events;

use App\Models\Sop;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SopStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $title;
    public string $body;
    public string $icon;
    public string $color;
    public string $status;
    public int $sopId;
    public string $sopName;
    public string $verifikatorName;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $recipient,
        Sop $sop,
        string $status,
        string $title,
        string $body,
        string $icon,
        string $color,
        string $verifikatorName
    ) {
        $this->sopId = $sop->id;
        $this->sopName = $sop->sop_name;
        $this->status = $status;
        $this->title = $title;
        $this->body = $body;
        $this->icon = $icon;
        $this->color = $color;
        $this->verifikatorName = $verifikatorName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->recipient->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'sop.status.changed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'color' => $this->color,
            'status' => $this->status,
            'sop_id' => $this->sopId,
            'sop_name' => $this->sopName,
            'verifikator' => $this->verifikatorName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
