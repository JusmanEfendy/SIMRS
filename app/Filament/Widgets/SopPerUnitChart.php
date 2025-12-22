<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class SopPerUnitChart extends ChartWidget
{
    protected static ?string $heading = '📊 Jumlah SOP per Unit';

    protected static ?string $description = 'Distribusi SOP di setiap unit kerja dalam direktorat Anda';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '400px';

    protected int | string | array $columnSpan = 'full';

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
        $activeCounts = [];
        $expiredCounts = [];

        foreach ($units as $unit) {
            // Full name for better readability in horizontal layout
            $labels[] = $unit->unit_name;

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
                    'label' => '✅ SOP Aktif',
                    'data' => $activeCounts,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.85)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 0,
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                    'barPercentage' => 0.7,
                    'categoryPercentage' => 0.8,
                ],
                [
                    'label' => '⚠️ SOP Kadaluarsa',
                    'data' => $expiredCounts,
                    'backgroundColor' => 'rgba(251, 146, 60, 0.85)',
                    'borderColor' => 'rgba(251, 146, 60, 1)',
                    'borderWidth' => 0,
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                    'barPercentage' => 0.7,
                    'categoryPercentage' => 0.8,
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
            'indexAxis' => 'y', // Horizontal bar chart
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'rectRounded',
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#fff',
                    'bodyColor' => '#e5e7eb',
                    'borderColor' => 'rgba(75, 85, 99, 0.3)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'padding' => 12,
                    'displayColors' => true,
                    'usePointStyle' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(156, 163, 175, 0.15)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'font' => [
                            'size' => 11,
                        ],
                        'color' => 'rgba(107, 114, 128, 0.8)',
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                        'color' => 'rgba(55, 65, 81, 0.9)',
                        'padding' => 8,
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
            ],
            'layout' => [
                'padding' => [
                    'top' => 10,
                    'right' => 20,
                    'bottom' => 10,
                    'left' => 10,
                ],
            ],
            'animation' => [
                'duration' => 750,
                'easing' => 'easeOutQuart',
            ],
        ];
    }
}
