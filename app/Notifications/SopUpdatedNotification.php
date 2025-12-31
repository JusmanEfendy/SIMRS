<?php

namespace App\Notifications;

use App\Models\Sop;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SopUpdatedNotification extends Notification
{
    use Queueable;

    protected Sop $sop;
    protected string $updaterName;
    protected array $changedFields;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sop $sop, string $updaterName, array $changedFields = [])
    {
        $this->sop = $sop;
        $this->updaterName = $updaterName;
        $this->changedFields = $changedFields;
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
        
        // Build change description
        $fieldLabels = [
            'sop_name' => 'Nama SOP',
            'sk_number' => 'Nomor SK',
            'status' => 'Status',
            'file_path' => 'File',
            'approval_date' => 'Tanggal Pengesahan',
            'start_date' => 'Tanggal Berlaku',
            'expired' => 'Tanggal Kadaluarsa',
            'desc' => 'Deskripsi',
            'type_sop' => 'Tipe SOP',
            'id_unit' => 'Unit',
        ];
        
        $changedLabels = array_map(
            fn($field) => $fieldLabels[$field] ?? $field,
            $this->changedFields
        );
        
        $changeText = !empty($changedLabels) 
            ? 'Perubahan: ' . implode(', ', $changedLabels)
            : 'Data SOP telah diperbarui';

        return [
            'title' => '✏️ SOP Diperbarui',
            'body' => "SOP \"{$this->sop->sop_name}\" (SK: {$this->sop->sk_number}) telah diperbarui oleh {$this->updaterName}. {$changeText}",
            'icon' => 'heroicon-o-pencil-square',
            'iconColor' => 'info',
            'status' => 'info',
            'duration' => 'persistent',
            'format' => 'filament',
            'sop_id' => $this->sop->id,
            'sop_name' => $this->sop->sop_name,
            'sk_number' => $this->sop->sk_number,
            'changed_fields' => $this->changedFields,
            'updater' => $this->updaterName,
            'updated_at' => now()->format('d M Y, H:i'),
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
