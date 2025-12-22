<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected static ?string $pollingInterval = '30s';

    protected function getColumns(): int
    {
        return 4;
    }

    /**
     * Only show for Unit role.
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Unit');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $unitId = $user->id_unit;

        // If no unit assigned, show zeros
        if (!$unitId) {
            return $this->getEmptyStats();
        }

        // Count SOPs for this unit
        $totalSop = Sop::where('id_unit', $unitId)->count();
        $activeSop = Sop::where('id_unit', $unitId)->where('status', 'Aktif')->count();
        $expiredSop = Sop::where('id_unit', $unitId)->where('status', 'Kadaluarsa')->count();
        
        // Expiring soon (≤ 90 days)
        $expiringSoon = Sop::where('id_unit', $unitId)
            ->where('status', 'Aktif')
            ->where('days_left', '<=', 90)
            ->where('days_left', '>', 0)
            ->count();

        // Needs annual review (SOPs not updated for > 1 year)
        $needsAnnualReview = Sop::where('id_unit', $unitId)
            ->where('status', 'Aktif')
            ->where('updated_at', '<=', now()->subYear())
            ->count();

        return [
            Stat::make('📋 Total SOP', $totalSop)
                ->description('Dokumen SOP unit Anda')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->chart($this->getTotalChart($unitId)),

            Stat::make('✅ Aktif', $activeSop)
                ->description($totalSop > 0 ? round(($activeSop / $totalSop) * 100) . '% dari total' : '0%')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->chart($this->getActiveChart($unitId)),

            Stat::make('⏰ Segera Expired', $expiringSoon)
                ->description('≤ 90 hari lagi')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('❌ Kadaluarsa', $expiredSop)
                ->description('Perlu diperbaharui')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }

    /**
     * Get empty stats when no unit assigned.
     */
    protected function getEmptyStats(): array
    {
        return [
            Stat::make('📋 Total SOP', 0)
                ->description('Dokumen SOP unit Anda')
                ->icon('heroicon-o-document-text')
                ->color('gray'),

            Stat::make('✅ Aktif', 0)
                ->description('0%')
                ->icon('heroicon-o-check-circle')
                ->color('gray'),

            Stat::make('⏰ Segera Expired', 0)
                ->description('≤ 90 hari lagi')
                ->icon('heroicon-o-clock')
                ->color('gray'),

            Stat::make('❌ Kadaluarsa', 0)
                ->description('Perlu diperbaharui')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('gray'),
        ];
    }

    /**
     * Get chart data for total SOPs trend over last 7 days.
     */
    protected function getTotalChart($unitId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $data[] = Sop::where('id_unit', $unitId)
                ->whereDate('created_at', '<=', $date)
                ->count();
        }
        return $data;
    }

    /**
     * Get chart data for active SOPs trend over last 7 days.
     */
    protected function getActiveChart($unitId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $data[] = Sop::where('id_unit', $unitId)
                ->where('status', 'Aktif')
                ->whereDate('created_at', '<=', $date)
                ->count();
        }
        return $data;
    }
}
