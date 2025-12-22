<?php

namespace App\Notifications;

use App\Models\Sop;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SopReviewReminderNotification extends Notification
{
    use Queueable;

    protected Sop $sop;
    protected string $type; // 'annual_review' or 'triennial_update'
    protected int $yearsActive;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sop $sop, string $type, int $yearsActive)
    {
        $this->sop = $sop;
        $this->type = $type;
        $this->yearsActive = $yearsActive;
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
        // Build URL manually to avoid Filament context issues in console
        $viewUrl = '/admin/sops/' . $this->sop->id;
        
        if ($this->type === 'triennial_update') {
            return [
                'title' => '🔄 SOP Perlu Diperbarui (3 Tahun)',
                'body' => "SOP \"{$this->sop->sop_name}\" (SK: {$this->sop->sk_number}) telah aktif selama {$this->yearsActive} tahun. Segera lakukan pembaruan dokumen SOP sesuai ketentuan.",
                'icon' => 'heroicon-o-arrow-path',
                'iconColor' => 'danger',
                'status' => 'danger',
                'duration' => 'persistent',
                'format' => 'filament',
                'sop_id' => $this->sop->id,
                'sop_name' => $this->sop->sop_name,
                'sk_number' => $this->sop->sk_number,
                'type' => $this->type,
                'years_active' => $this->yearsActive,
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
        
        // Annual review notification
        return [
            'title' => '📋 Waktunya Meninjau SOP (1 Tahun)',
            'body' => "SOP \"{$this->sop->sop_name}\" (SK: {$this->sop->sk_number}) telah aktif selama {$this->yearsActive} tahun. Silakan tinjau kembali dokumen ini untuk memastikan relevansi dan kepatuhan.",
            'icon' => 'heroicon-o-clipboard-document-check',
            'iconColor' => 'warning',
            'status' => 'warning',
            'duration' => 'persistent',
            'format' => 'filament',
            'sop_id' => $this->sop->id,
            'sop_name' => $this->sop->sop_name,
            'sk_number' => $this->sop->sk_number,
            'type' => $this->type,
            'years_active' => $this->yearsActive,
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
}

