<?php

namespace App\Notifications;

use App\Models\Sop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SopStatusNotification extends Notification
{
    use Queueable;

    protected Sop $sop;
    protected string $status;
    protected ?string $feedback;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sop $sop, string $status, ?string $feedback = null)
    {
        $this->sop = $sop;
        $this->status = $status;
        $this->feedback = $feedback;
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
        $isApproved = $this->status === 'approved';
        
        return [
            'title' => $isApproved 
                ? 'SOP Disetujui ✅' 
                : 'SOP Ditolak ❌',
            'body' => $isApproved
                ? "SOP \"{$this->sop->sop_name}\" telah disetujui oleh verifikator."
                : "SOP \"{$this->sop->sop_name}\" ditolak. Alasan: {$this->feedback}",
            'icon' => $isApproved ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle',
            'iconColor' => $isApproved ? 'success' : 'danger',
            'sop_id' => $this->sop->id,
            'sop_name' => $this->sop->sop_name,
            'status' => $this->status,
            'feedback' => $this->feedback,
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
