<?php

namespace App\Filament\Resources\SopResource\Pages;

use App\Filament\Resources\SopResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ViewSop extends ViewRecord
{
    protected static string $resource = SopResource::class;

    /**
     * Simpan notifikasi langsung ke database dengan tampilan yang lebih menarik
     */
    private function sendDatabaseNotification(
        $recipient, 
        string $title, 
        string $body, 
        string $icon, 
        string $color,
        ?int $sopId = null,
        ?string $status = null
    ): void {
        if (!$recipient) {
            Log::warning('Cannot send notification: recipient is null');
            return;
        }

        $verifikator = auth()->user();
        $viewUrl = $sopId ? SopResource::getUrl('view', ['record' => $sopId]) : null;

        try {
            // Data notifikasi dengan format Filament yang lengkap
            $notificationData = [
                'title' => $title,
                'body' => $body,
                'icon' => $icon,
                'iconColor' => $color,
                'status' => $color,
                'duration' => 'persistent',
                'format' => 'filament',
                // Data tambahan
                'sop_id' => $sopId,
                'verifikator' => $verifikator?->name ?? 'System',
                'verified_at' => now()->format('d M Y, H:i'),
                'notification_status' => $status,
            ];

            // Tambahkan actions jika ada URL
            if ($viewUrl) {
                $notificationData['actions'] = [
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
                ];
            }

            DB::table('notifications')->insert([
                'id' => Str::uuid()->toString(),
                'type' => 'Filament\\Notifications\\DatabaseNotification',
                'notifiable_type' => get_class($recipient),
                'notifiable_id' => $recipient->id,
                'data' => json_encode($notificationData),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Database notification sent successfully', [
                'recipient_id' => $recipient->id,
                'title' => $title,
                'sop_id' => $sopId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send database notification', [
                'error' => $e->getMessage(),
                'recipient_id' => $recipient->id ?? null,
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('approve')
                ->label('Setujui')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Persetujuan')
                ->modalDescription('Apakah Anda yakin ingin menyetujui SOP ini?')
                ->modalIcon('heroicon-o-check-circle')
                ->modalIconColor('success')
                ->visible(fn () => auth()->user()->hasRole('Verifikator') && $this->record->status === 'In Review')
                ->action(function () {
                    $this->record->update([
                        'status' => 'Approve',
                        'approval_date' => now(),
                    ]);

                    $recipient = $this->record->user;
                    $verifikator = auth()->user();
                    
                    Log::info('SOP Approved', [
                        'sop_id' => $this->record->id,
                        'sop_name' => $this->record->sop_name,
                        'recipient_id' => $recipient?->id,
                        'recipient_name' => $recipient?->name,
                        'verifikator' => $verifikator?->name,
                    ]);

                    // Kirim notifikasi dengan tampilan yang lebih menarik
                    $this->sendDatabaseNotification(
                        recipient: $recipient,
                        title: '🎉 SOP Disetujui!',
                        body: "Selamat! SOP \"{$this->record->sop_name}\" telah disetujui oleh {$verifikator->name}. SOP Anda sekarang aktif dan dapat digunakan.",
                        icon: 'heroicon-o-check-badge',
                        color: 'success',
                        sopId: $this->record->id,
                        status: 'approved'
                    );

                    Notification::make()
                        ->title('✅ SOP Berhasil Disetujui')
                        ->body($recipient ? "Notifikasi telah dikirim ke {$recipient->name}" : "Peringatan: Pembuat SOP tidak ditemukan!")
                        ->success()
                        ->icon('heroicon-o-check-badge')
                        ->duration(5000)
                        ->send();

                    $this->redirect(SopResource::getUrl('index'));
                }),

            Actions\Action::make('Rejected')
                ->label('Tolak')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('danger')
                ->visible(fn () => auth()->user()->hasRole('Verifikator') && $this->record->status === 'In Review')
                ->form([
                    \Filament\Forms\Components\Textarea::make('feedback')
                        ->label('Alasan Penolakan')
                        ->placeholder('Jelaskan alasan penolakan SOP ini agar pembuat dapat memperbaikinya...')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull()
                        ->helperText('Berikan alasan yang jelas agar pembuat SOP dapat memperbaiki dokumennya.'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'Rejected',
                        'feedback' => $data['feedback'],
                    ]);

                    $recipient = $this->record->user;
                    $verifikator = auth()->user();
                    
                    Log::info('SOP Rejected', [
                        'sop_id' => $this->record->id,
                        'sop_name' => $this->record->sop_name,
                        'recipient_id' => $recipient?->id,
                        'recipient_name' => $recipient?->name,
                        'verifikator' => $verifikator?->name,
                        'feedback' => $data['feedback'],
                    ]);

                    // Kirim notifikasi dengan tampilan yang lebih menarik
                    $this->sendDatabaseNotification(
                        recipient: $recipient,
                        title: '⚠️ SOP Perlu Revisi',
                        body: "SOP \"{$this->record->sop_name}\" memerlukan perbaikan.\n\n📝 Catatan dari {$verifikator->name}:\n\"{$data['feedback']}\"\n\nSilakan perbaiki dan ajukan kembali.",
                        icon: 'heroicon-o-exclamation-triangle',
                        color: 'danger',
                        sopId: $this->record->id,
                        status: 'rejected'
                    );

                    Notification::make()
                        ->title('❌ SOP Ditolak')
                        ->body($recipient ? "Notifikasi penolakan telah dikirim ke {$recipient->name}" : "Peringatan: Pembuat SOP tidak ditemukan!")
                        ->warning()
                        ->icon('heroicon-o-exclamation-triangle')
                        ->duration(5000)
                        ->send();

                    $this->redirect(SopResource::getUrl('index'));
                }),

            Action::make('viewPdf')
                ->label(fn () => auth()->user()->hasRole('Unit') ? 'Lihat SOP' : 'Review SOP')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->modalHeading('Preview Dokumen SOP')
                ->modalWidth('7xl')
                ->modalContent(function ($record) {
                    return view('filament.components.pdf-viewer', [
                        'url' => Storage::url($record->file_path),
                        'record' => $record,
                    ]);
                })
                ->visible(fn ($record) => filled($record->file_path)),
        ];
    }
}




