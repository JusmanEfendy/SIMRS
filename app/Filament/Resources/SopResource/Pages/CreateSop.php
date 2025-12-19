<?php

namespace App\Filament\Resources\SopResource\Pages;

use App\Filament\Resources\SopResource;
use App\Models\User;
use App\Notifications\SopPublishedNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CreateSop extends CreateRecord
{
    protected static string $resource = SopResource::class;

    /**
     * Send notifications after SOP is created.
     */
    protected function afterCreate(): void
    {
        $sop = $this->record;
        $publisher = auth()->user();
        
        // Load unit relation if not already loaded
        $sop->load('unit.directorate');
        
        // Get recipients: all users with same id_unit (exclude publisher)
        $unitUsers = User::where('id_unit', $sop->id_unit)
            ->where('id', '!=', $publisher->id)
            ->get();
        
        // Get directorate managers for the unit's directorate
        $directorateManagers = collect();
        if ($sop->unit && $sop->unit->directorate) {
            $directorateManagers = User::where('dir_id', $sop->unit->directorate->id)
                ->where('id', '!=', $publisher->id)
                ->get();
        }
        
        // Merge and deduplicate recipients
        $recipients = $unitUsers->merge($directorateManagers)->unique('id');
        
        // Send notification to all recipients
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new SopPublishedNotification($sop, $publisher->name));
            
            Log::info('SOP Published notifications sent', [
                'sop_id' => $sop->id,
                'sop_name' => $sop->sop_name,
                'unit_id' => $sop->id_unit,
                'publisher' => $publisher->name,
                'recipients_count' => $recipients->count(),
                'recipient_ids' => $recipients->pluck('id')->toArray(),
            ]);
        } else {
            Log::info('No recipients for SOP Published notification', [
                'sop_id' => $sop->id,
                'sop_name' => $sop->sop_name,
            ]);
        }
    }
}
