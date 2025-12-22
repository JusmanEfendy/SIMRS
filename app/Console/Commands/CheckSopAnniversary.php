<?php

namespace App\Console\Commands;

use App\Models\Sop;
use App\Models\User;
use App\Notifications\SopReviewReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSopAnniversary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sop:check-anniversary {--test : Jalankan mode test untuk mengecek semua SOP tanpa melihat tanggal anniversary}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek SOP untuk reminder review tahunan (1 tahun) dan update 3 tahunan berdasarkan tanggal berlaku';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();
        $testMode = $this->option('test');
        
        $this->info("Mengecek reminder anniversary SOP...");
        $this->info("Tanggal hari ini: " . $today->format('d M Y'));
        
        if ($testMode) {
            $this->warn("⚠️  MODE TEST: Akan mengecek semua SOP aktif tanpa melihat tanggal anniversary");
        }
        
        // Get all active SOPs
        $activeSops = Sop::where('status', 'Aktif')->get();
        
        $annualReviewCount = 0;
        $triennialUpdateCount = 0;
        $notificationCount = 0;
        
        foreach ($activeSops as $sop) {
            $startDate = Carbon::parse($sop->start_date);
            $yearsActive = $startDate->diffInYears($today);
            
            // Calculate if today is anniversary of start_date
            $isAnniversaryToday = $startDate->format('m-d') === $today->format('m-d');
            
            // In test mode, process all SOPs
            if ($testMode) {
                $isAnniversaryToday = true;
            }
            
            if (!$isAnniversaryToday) {
                continue;
            }
            
            // Check for 3-year update reminder (every 3 years: 3, 6, 9, etc.)
            if ($yearsActive > 0 && $yearsActive % 3 === 0) {
                $triennialUpdateCount++;
                $this->sendNotifications($sop, 'triennial_update', $yearsActive, $notificationCount);
                $this->line("  🔄 Update 3 Tahunan: {$sop->sop_name} ({$yearsActive} tahun aktif)");
            }
            // Check for annual review reminder (every year, but not on 3-year marks)
            elseif ($yearsActive > 0) {
                $annualReviewCount++;
                $this->sendNotifications($sop, 'annual_review', $yearsActive, $notificationCount);
                $this->line("  📋 Review Tahunan: {$sop->sop_name} ({$yearsActive} tahun aktif)");
            }
        }
        
        $this->newLine();
        $this->info("Ringkasan:");
        $this->line("  - Total SOP aktif yang dicek: {$activeSops->count()}");
        $this->line("  - Reminder review tahunan: {$annualReviewCount}");
        $this->line("  - Reminder update 3 tahunan: {$triennialUpdateCount}");
        $this->line("  - Total notifikasi terkirim: {$notificationCount}");
        
        $this->newLine();
        $this->info("Pengecekan anniversary SOP selesai!");
        
        return Command::SUCCESS;
    }
    
    /**
     * Send notifications to all users in the SOP's unit.
     */
    protected function sendNotifications(Sop $sop, string $type, int $yearsActive, int &$count): void
    {
        // Get users from the same unit as the SOP
        $users = User::where('id_unit', $sop->id_unit)->get();
        
        foreach ($users as $user) {
            $user->notify(new SopReviewReminderNotification($sop, $type, $yearsActive));
            $count++;
        }
        
        if ($users->isEmpty()) {
            $this->warn("    ⚠️ Tidak ada user ditemukan untuk unit: {$sop->id_unit}");
        } else {
            $this->line("    → Terkirim ke {$users->count()} user");
        }
    }
}

