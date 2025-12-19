<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerifikatorStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    /**
     * Only show for Verifikator role.
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Verifikator');
    }

    protected function getStats(): array
    {
        // Total SOP
        $totalSop = Sop::count();
        
        // SOP Aktif
        $activeSop = Sop::where('status', 'Aktif')->count();
        
        // SOP Kadaluarsa
        $expiredSop = Sop::where('status', 'Kadaluarsa')->count();
        
        // Segera Kadaluarsa (≤ 90 hari)
        $expiringSoon = Sop::where('status', 'Aktif')
            ->where('days_left', '<=', 90)
            ->where('days_left', '>', 0)
            ->count();
        
        // Perlu Review Tahunan (SOP yang sudah melewati 1 tahun sejak dibuat/diupdate)
        $needsAnnualReview = Sop::where('status', 'Aktif')
            ->where('updated_at', '<=', now()->subYear())
            ->count();

        return [
            Stat::make('Total Dokumen SOP', $totalSop)
                ->description('Semua dokumen SOP')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->chart($this->getTotalChart()),

            Stat::make('SOP Aktif', $activeSop)
                ->description(round(($totalSop > 0 ? ($activeSop / $totalSop) * 100 : 0)) . '% dari total')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Perlu Review Tahunan', $needsAnnualReview)
                ->description('Belum diupdate > 1 tahun')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),

            Stat::make('Segera Kadaluarsa', $expiringSoon)
                ->description('≤ 90 hari lagi')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('SOP Kadaluarsa', $expiredSop)
                ->description('Perlu diperbaharui')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }

    /**
     * Get chart data for total SOPs trend over last 7 days.
     */
    protected function getTotalChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $data[] = Sop::whereDate('created_at', '<=', $date)->count();
        }
        return $data;
    }
}
