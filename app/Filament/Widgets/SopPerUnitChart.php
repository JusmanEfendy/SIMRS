<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class SopPerUnitChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah SOP per Unit';

    protected static ?string $description = 'Distribusi SOP di setiap unit kerja';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

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
                        'label' => 'SOP',
                        'data' => [],
                        'backgroundColor' => [],
                    ],
                ],
                'labels' => ['Tidak ada data'],
            ];
        }

        // Get units under this directorate
        $units = Unit::where('dir_id', $dirId)
            ->withCount(['sops'])
            ->orderBy('sops_count', 'desc')
            ->get();

        $labels = [];
        $sopCounts = [];
        $activeCounts = [];
        $expiredCounts = [];
        $colors = [
            'rgba(59, 130, 246, 0.8)',  // blue
            'rgba(16, 185, 129, 0.8)',  // green
            'rgba(245, 158, 11, 0.8)',  // amber
            'rgba(239, 68, 68, 0.8)',   // red
            'rgba(139, 92, 246, 0.8)',  // purple
            'rgba(236, 72, 153, 0.8)',  // pink
            'rgba(20, 184, 166, 0.8)',  // teal
            'rgba(249, 115, 22, 0.8)',  // orange
        ];

        foreach ($units as $index => $unit) {
            $labels[] = strlen($unit->unit_name) > 15
                ? substr($unit->unit_name, 0, 15) . '...'
                : $unit->unit_name;

            $sopCounts[] = $unit->sops_count;

            $activeCounts[] = Sop::where('id_unit', $unit->id_unit)
                ->where('status', 'Aktif')
                ->count();

            $expiredCounts[] = Sop::where('id_unit', $unit->id_unit)
                ->where('status', 'Kadaluarsa')
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'SOP Aktif',
                    'data' => $activeCounts,
                    'backgroundColor' => 'rgba(15, 221, 94, 1)',
                    'borderColor' => 'rgba(15, 221, 94, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'SOP Kadaluarsa',
                    'data' => $expiredCounts,
                    'backgroundColor' => 'rgba(200, 133, 217, 1)',
                    'borderColor' => 'rgba(200, 133, 217, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
