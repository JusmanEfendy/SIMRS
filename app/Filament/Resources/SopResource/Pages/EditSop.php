<?php

namespace App\Filament\Resources\SopResource\Pages;

use App\Filament\Resources\SopResource;
use App\Models\User;
use App\Notifications\SopUpdatedNotification;
use App\Notifications\SopDeletedNotification;
use App\Notifications\SopRestoredNotification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class EditSop extends EditRecord
{
    protected static string $resource = SopResource::class;

    /**
     * Store original data before update for comparison.
     */
    protected array $originalData = [];

    /**
     * Store data for delete notification (before record is deleted).
     */
    protected array $deleteNotificationData = [];

    /**
     * Called before the form is filled with the record data.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Store original data for comparison
        $this->originalData = $this->record->only([
            'sop_name', 'sk_number', 'status', 'file_path', 
            'approval_date', 'start_date', 'expired', 'desc', 
            'type_sop', 'id_unit'
        ]);
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function () {
                    // Store SOP data and recipients BEFORE delete
                    $this->prepareDeleteNotificationData();
                })
                ->after(function () {
                    // Send notification AFTER delete using stored data
                    $this->sendDeleteNotification();
                }),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make()
                ->after(function () {
                    $this->sendRestoreNotification();
                }),
        ];
    }

    /**
     * Send notifications after SOP is updated.
     */
    protected function afterSave(): void
    {
        $sop = $this->record;
        $updater = auth()->user();

        // Reload the record to get fresh data
        $sop->refresh();
        
        // Load unit relation
        $sop->load('unit.directorate');

        // Get changed fields by comparing with dirty attributes
        $changedFields = array_keys($sop->getChanges());
        
        // Filter only trackable fields
        $trackableFields = ['sop_name', 'sk_number', 'status', 'file_path', 'approval_date', 'start_date', 'expired', 'desc', 'type_sop', 'id_unit'];
        $changedFields = array_intersect($changedFields, $trackableFields);

        Log::info('SOP Update - afterSave triggered', [
            'sop_id' => $sop->id,
            'sop_name' => $sop->sop_name,
            'updater' => $updater->name,
            'changed_fields' => $changedFields,
        ]);

        // Get recipients
        $recipients = $this->getNotificationRecipients($sop, $updater->id);

        Log::info('SOP Update - Recipients found', [
            'recipients_count' => $recipients->count(),
            'recipient_ids' => $recipients->pluck('id')->toArray(),
        ]);

        // Send notification (even if no specific changed fields detected, still notify)
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new SopUpdatedNotification($sop, $updater->name, $changedFields));
            Log::info('SOP Update notification sent successfully');
        }
    }

    /**
     * Prepare data for delete notification BEFORE the record is deleted.
     */
    protected function prepareDeleteNotificationData(): void
    {
        $sop = $this->record;
        $deleter = auth()->user();

        // Load unit relation BEFORE delete
        $sop->load('unit.directorate');

        Log::info('SOP Delete - Preparing notification data', [
            'sop_id' => $sop->id,
            'sop_name' => $sop->sop_name,
            'id_unit' => $sop->id_unit,
            'unit_name' => $sop->unit?->unit_name,
            'directorate_id' => $sop->unit?->directorate?->id,
        ]);

        // Get recipients
        $recipients = $this->getNotificationRecipients($sop, $deleter->id);

        Log::info('SOP Delete - Recipients found', [
            'recipients_count' => $recipients->count(),
            'recipient_ids' => $recipients->pluck('id')->toArray(),
        ]);

        // Store SOP data
        $this->deleteNotificationData = [
            'sop_name' => $sop->sop_name,
            'sk_number' => $sop->sk_number,
            'unit_name' => $sop->unit?->unit_name ?? 'Unknown',
            'deleter_name' => $deleter->name,
            'recipients' => $recipients,
        ];
    }

    /**
     * Send notification when SOP is deleted (using stored data).
     */
    protected function sendDeleteNotification(): void
    {
        // Use stored data from before delete
        $data = $this->deleteNotificationData;

        Log::info('SOP Delete - sendDeleteNotification called', [
            'has_data' => !empty($data),
            'has_recipients' => isset($data['recipients']),
            'recipients_count' => isset($data['recipients']) ? $data['recipients']->count() : 0,
        ]);

        if (empty($data) || empty($data['recipients']) || $data['recipients']->isEmpty()) {
            Log::warning('SOP Delete - No recipients, notification not sent');
            return;
        }

        Notification::send($data['recipients'], new SopDeletedNotification(
            $data['sop_name'],
            $data['sk_number'],
            $data['unit_name'],
            $data['deleter_name']
        ));

        Log::info('SOP Delete notification sent successfully');
    }

    /**
     * Send notification when SOP is restored.
     */
    protected function sendRestoreNotification(): void
    {
        $sop = $this->record;
        $restorer = auth()->user();

        // Load unit relation
        $sop->load('unit.directorate');

        // Get recipients
        $recipients = $this->getNotificationRecipients($sop, $restorer->id);

        // Send notification
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new SopRestoredNotification($sop, $restorer->name));
        }
    }

    /**
     * Get notification recipients (Unit users + Directorate managers).
     */
    protected function getNotificationRecipients($sop, int $excludeUserId)
    {
        // Load relations if needed
        if (!$sop->relationLoaded('unit')) {
            $sop->load('unit.directorate');
        }

        // Get users from the same unit
        $unitUsers = User::where('id_unit', $sop->id_unit)
            ->where('id', '!=', $excludeUserId)
            ->get();

        // Get directorate managers
        $directorateManagers = collect();
        if ($sop->unit && $sop->unit->directorate) {
            $directorateManagers = User::where('dir_id', $sop->unit->directorate->id)
                ->where('id', '!=', $excludeUserId)
                ->get();
        }

        // Merge and deduplicate
        return $unitUsers->merge($directorateManagers)->unique('id');
    }
}
