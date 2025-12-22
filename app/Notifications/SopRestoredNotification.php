<?php

namespace App\Notifications;

use App\Models\Sop;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SopRestoredNotification extends Notification
{
    use Queueable;

    protected Sop $sop;
    protected string $restorerName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sop $sop, string $restorerName)
    {
        $this->sop = $sop;
        $this->restorerName = $restorerName;
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
        // Generate URL based on user role
        $viewUrl = $this->getUrlForUser($notifiable);

        return [
            'title' => '♻️ SOP Dipulihkan',
            'body' => "SOP \"{$this->sop->sop_name}\" (SK: {$this->sop->sk_number}) telah dipulihkan oleh {$this->restorerName}.",
            'icon' => 'heroicon-o-arrow-path',
            'iconColor' => 'success',
            'status' => 'success',
            'duration' => 'persistent',
            'format' => 'filament',
            'sop_id' => $this->sop->id,
            'sop_name' => $this->sop->sop_name,
            'sk_number' => $this->sop->sk_number,
            'restorer' => $this->restorerName,
            'restored_at' => now()->format('d M Y, H:i'),
            'actions' => [
                [
                    'name' => 'view',
                    'label' => 'Lihat SOP',
                    'url' => $viewUrl,
                    'color' => 'primary',
                    'icon' => 'heroicon-o-eye',
                    'shouldOpenInNewTab' => false,
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

    /**
     * Get the appropriate URL based on user role.
     */
    protected function getUrlForUser(object $notifiable): string
    {
        if ($notifiable->hasRole('Unit')) {
            return '/unit/sops/' . $this->sop->id;
        } elseif ($notifiable->hasRole('Direksi')) {
            return '/direksi/sops/' . $this->sop->id;
        } elseif ($notifiable->hasRole('Verifikator')) {
            return '/verifikator/sops/' . $this->sop->id;
        }
        return '/admin/sops/' . $this->sop->id;
    }
}
