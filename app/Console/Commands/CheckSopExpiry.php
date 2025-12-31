<?php

namespace App\Console\Commands;

use App\Models\Sop;
use App\Models\User;
use App\Notifications\SopExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSopExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sop:check-expiry {--days=30 : Days before expiry to send warning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SOPs that are expiring soon or already expired and send notifications to related units';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $warningDays = (int) $this->option('days');
        $today = Carbon::today();
        
        $this->info("Checking SOP expiry status...");
        $this->info("Warning threshold: {$warningDays} days before expiry");
        
        // Get SOPs that are expiring within warning period
        $expiringSops = Sop::where('status', 'Aktif')
            ->whereDate('expired', '<=', $today->copy()->addDays($warningDays))
            ->whereDate('expired', '>=', $today)
            ->get();
        
        // Get SOPs that have already expired but still marked as Aktif
        $expiredSops = Sop::where('status', 'Aktif')
            ->whereDate('expired', '<', $today)
            ->get();
        
        $notificationCount = 0;
        
        // Send warning notifications for expiring SOPs
        foreach ($expiringSops as $sop) {
            $daysLeft = (int) $today->diffInDays($sop->expired, false);
            
            // Find all users that belong to this SOP's unit
            $users = User::where('id_unit', $sop->id_unit)->get();
            
            foreach ($users as $user) {
                $user->notify(new SopExpiryNotification($sop, 'warning', $daysLeft));
                $notificationCount++;
            }
            
            if ($users->count() > 0) {
                $this->line("  ⏰ Warning sent for: {$sop->sop_name} ({$daysLeft} days left) to {$users->count()} user(s)");
            } else {
                $this->line("  ⚠️ No users found for unit: {$sop->id_unit}");
            }
        }
        
        // Send expired notifications and update status
        foreach ($expiredSops as $sop) {
            $daysLeft = (int) $today->diffInDays($sop->expired, false);
            
            // Update SOP status to Kadaluarsa
            $sop->update([
                'status' => 'Kadaluarsa',
                'days_left' => $daysLeft,
            ]);
            
            // Find all users that belong to this SOP's unit
            $users = User::where('id_unit', $sop->id_unit)->get();
            
            foreach ($users as $user) {
                $user->notify(new SopExpiryNotification($sop, 'expired', $daysLeft));
                $notificationCount++;
            }
            
            if ($users->count() > 0) {
                $this->line("  ⚠️ Expired notification sent for: {$sop->sop_name} to {$users->count()} user(s)");
            } else {
                $this->line("  ⚠️ No users found for unit: {$sop->id_unit}");
            }
        }
        
        $this->newLine();
        $this->info("Summary:");
        $this->line("  - SOPs expiring soon: {$expiringSops->count()}");
        $this->line("  - SOPs already expired: {$expiredSops->count()}");
        $this->line("  - Notifications sent: {$notificationCount}");
        
        $this->newLine();
        $this->info("SOP expiry check completed!");
        
        return Command::SUCCESS;
    }
}
