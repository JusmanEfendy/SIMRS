<?php

namespace App\Notifications;

use App\Models\Sop;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SopDeletedNotification extends Notification
{
    use Queueable;

    protected string $sopName;
    protected string $skNumber;
    protected string $unitName;
    protected string $deleterName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $sopName, string $skNumber, string $unitName, string $deleterName)
    {
        $this->sopName = $sopName;
        $this->skNumber = $skNumber;
        $this->unitName = $unitName;
        $this->deleterName = $deleterName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => '🗑️ SOP Dihapus',
            'body' => "SOP \"{$this->sopName}\" (SK: {$this->skNumber}) dari unit {$this->unitName} telah dihapus oleh {$this->deleterName}.",
            'icon' => 'heroicon-o-trash',
            'iconColor' => 'danger',
            'status' => 'danger',
            'duration' => 'persistent',
            'format' => 'filament',
            'sop_name' => $this->sopName,
            'sk_number' => $this->skNumber,
            'unit_name' => $this->unitName,
            'deleter' => $this->deleterName,
            'deleted_at' => now()->format('d M Y, H:i'),
        ];
    }

    /**
     * Get the Filament database notification representation.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
