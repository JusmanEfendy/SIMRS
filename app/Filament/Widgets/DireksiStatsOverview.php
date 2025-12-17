<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DireksiStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    /**
     * Only show for Direksi role.
     */
    public static function canView(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('Direksi')
        );
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $dirId = $user->dir_id;

        // If no directorate assigned, show zeros
        if (!$dirId) {
            return $this->getEmptyStats();
        }

        // Get unit IDs under this directorate
        $unitIds = Unit::where('dir_id', $dirId)->pluck('id_unit');

        // Count SOPs
        $totalSop = Sop::whereIn('id_unit', $unitIds)->count();
        $activeSop = Sop::whereIn('id_unit', $unitIds)->where('status', 'Aktif')->count();
        $expiredSop = Sop::whereIn('id_unit', $unitIds)->where('status', 'Kadaluarsa')->count();
        $expiringSoon = Sop::whereIn('id_unit', $unitIds)
            ->where('status', 'Aktif')
            ->where('days_left', '<=', 90)
            ->where('days_left', '>', 0)
            ->count();

        // Count units
        $totalUnits = Unit::where('dir_id', $dirId)->count();

        return [
            Stat::make('Total Unit', $totalUnits)
                ->description('Unit di bawah direktorat Anda')
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Total SOP', $totalSop)
                ->description('Semua dokumen SOP')
                ->icon('heroicon-o-document-text')
                ->color('gray'),

            Stat::make('SOP Aktif', $activeSop)
                ->description(round(($totalSop > 0 ? ($activeSop / $totalSop) * 100 : 0)) . '% dari total')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->chart($this->getActiveChart($dirId)),

            Stat::make('SOP Kadaluarsa', $expiredSop)
                ->description('Perlu diperbaharui')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Segera Kadaluarsa', $expiringSoon)
                ->description('≤ 90 hari lagi')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }

    protected function getEmptyStats(): array
    {
        return [
            Stat::make('Total Unit', 0)
                ->description('Belum ada direktorat yang terhubung')
                ->icon('heroicon-o-building-office-2')
                ->color('gray'),
            Stat::make('Total SOP', 0)
                ->icon('heroicon-o-document-text')
                ->color('gray'),
            Stat::make('SOP Aktif', 0)
                ->icon('heroicon-o-check-circle')
                ->color('gray'),
            Stat::make('SOP Kadaluarsa', 0)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('gray'),
            Stat::make('Segera Kadaluarsa', 0)
                ->icon('heroicon-o-clock')
                ->color('gray'),
        ];
    }

    /**
     * Get chart data for active SOPs trend.
     */
    protected function getActiveChart($dirId): array
    {
        $unitIds = Unit::where('dir_id', $dirId)->pluck('id_unit');

        // Get last 7 days SOP count
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $data[] = Sop::whereIn('id_unit', $unitIds)
                ->where('status', 'Aktif')
                ->whereDate('created_at', '<=', $date)
                ->count();
        }

        return $data;
    }
}
