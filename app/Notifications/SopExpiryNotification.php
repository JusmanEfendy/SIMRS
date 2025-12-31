<?php

namespace App\Notifications;

use App\Models\Sop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SopExpiryNotification extends Notification
{
    use Queueable;

    protected Sop $sop;
    protected string $type; // 'warning' or 'expired'
    protected int $daysLeft;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sop $sop, string $type, int $daysLeft)
    {
        $this->sop = $sop;
        $this->type = $type;
        $this->daysLeft = $daysLeft;
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
        $isExpired = $this->type === 'expired';
        
        if ($isExpired) {
            return [
                'title' => 'SOP Sudah Kadaluarsa ⚠️',
                'body' => "SOP \"{$this->sop->sop_name}\" (SK: {$this->sop->sk_number}) telah kadaluarsa. Segera lakukan pembaharuan dokumen SOP.",
                'icon' => 'heroicon-o-exclamation-triangle',
                'iconColor' => 'danger',
                'sop_id' => $this->sop->id,
                'sop_name' => $this->sop->sop_name,
                'sk_number' => $this->sop->sk_number,
                'type' => $this->type,
                'days_left' => $this->daysLeft,
            ];
        }
        
        return [
            'title' => 'SOP Akan Segera Kadaluarsa ⏰',
            'body' => "SOP \"{$this->sop->sop_name}\" (SK: {$this->sop->sk_number}) akan kadaluarsa dalam {$this->daysLeft} hari. Segera persiapkan pembaharuan dokumen.",
            'icon' => 'heroicon-o-clock',
            'iconColor' => 'warning',
            'sop_id' => $this->sop->id,
            'sop_name' => $this->sop->sop_name,
            'sk_number' => $this->sop->sk_number,
            'type' => $this->type,
            'days_left' => $this->daysLeft,
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
