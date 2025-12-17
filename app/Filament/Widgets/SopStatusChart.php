<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class SopStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status SOP';

    protected static ?string $description = 'Distribusi status SOP di direktorat Anda';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '250px';

    protected int | string | array $columnSpan = 1;

    /**
     * Only show for Direksi role.
     */
    public static function canView(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('Direksi')
        );
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $dirId = $user->dir_id;

        if (!$dirId) {
            return [
                'datasets' => [
                    [
                        'data' => [0, 0],
                        'backgroundColor' => ['#9ca3af', '#9ca3af'],
                    ],
                ],
                'labels' => ['Tidak ada data', ''],
            ];
        }

        // Get unit IDs under this directorate
        $unitIds = Unit::where('dir_id', $dirId)->pluck('id_unit');

        $activeSop = Sop::whereIn('id_unit', $unitIds)->where('status', 'Aktif')->count();
        $expiredSop = Sop::whereIn('id_unit', $unitIds)->where('status', 'Kadaluarsa')->count();

        return [
            'datasets' => [
                [
                    'data' => [$activeSop, $expiredSop],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)',  // green - Aktif
                        'rgba(239, 68, 68, 0.8)',   // red - Kadaluarsa
                    ],
                    'borderColor' => [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Aktif', 'Kadaluarsa'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%',
            'maintainAspectRatio' => false,
        ];
    }
}
