<?php

namespace App\Notifications;

use App\Models\Sop;
use App\Filament\Resources\SopResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SopPublishedNotification extends Notification
{
    use Queueable;

    protected Sop $sop;
    protected string $publisherName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sop $sop, string $publisherName)
    {
        $this->sop = $sop;
        $this->publisherName = $publisherName;
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
        $viewUrl = SopResource::getUrl('view', ['record' => $this->sop->id]);

        return [
            'title' => '📢 SOP Baru Diterbitkan',
            'body' => "SOP \"{$this->sop->sop_name}\" telah diterbitkan oleh {$this->publisherName}. Unit: {$this->sop->unit?->unit_name}",
            'icon' => 'heroicon-o-document-plus',
            'iconColor' => 'info',
            'status' => 'info',
            'duration' => 'persistent',
            'format' => 'filament',
            'sop_id' => $this->sop->id,
            'sop_name' => $this->sop->sop_name,
            'sk_number' => $this->sop->sk_number,
            'unit_name' => $this->sop->unit?->unit_name,
            'publisher' => $this->publisherName,
            'published_at' => now()->format('d M Y, H:i'),
            'actions' => [
                [
                    'name' => 'view',
                    'label' => 'Lihat SOP',
                    'url' => $viewUrl,
                    'color' => 'primary',
                    'icon' => 'heroicon-o-eye',
                    'shouldOpenInNewTab' => false,
                ],
                [
                    'name' => 'mark_as_read',
                    'label' => 'Tandai Dibaca',
                    'color' => 'gray',
                    'close' => true,
                    'isMarkAsRead' => true,
                ],
            ],
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
