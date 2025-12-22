<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class SopStatusChart extends ChartWidget
{
    protected static ?string $heading = '📈 Ringkasan Status SOP';

    protected static ?string $description = 'Perbandingan SOP Aktif vs Kadaluarsa di direktorat Anda';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '200px';

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
                        'label' => 'SOP Aktif',
                        'data' => [0],
                        'backgroundColor' => 'rgba(156, 163, 175, 0.5)',
                    ],
                ],
                'labels' => ['Tidak ada data'],
            ];
        }

        // Get unit IDs under this directorate
        $unitIds = Unit::where('dir_id', $dirId)->pluck('id_unit');

        $activeSop = Sop::whereIn('id_unit', $unitIds)->where('status', 'Aktif')->count();
        $expiredSop = Sop::whereIn('id_unit', $unitIds)->where('status', 'Kadaluarsa')->count();
        $totalSop = $activeSop + $expiredSop;

        return [
            'datasets' => [
                [
                    'label' => "✅ Aktif ({$activeSop})",
                    'data' => [$activeSop],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.85)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 0,
                    'borderRadius' => [
                        'topLeft' => 8,
                        'bottomLeft' => 8,
                        'topRight' => $expiredSop === 0 ? 8 : 0,
                        'bottomRight' => $expiredSop === 0 ? 8 : 0,
                    ],
                    'borderSkipped' => false,
                    'barPercentage' => 0.6,
                    'categoryPercentage' => 1,
                ],
                [
                    'label' => "⚠️ Kadaluarsa ({$expiredSop})",
                    'data' => [$expiredSop],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.85)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 0,
                    'borderRadius' => [
                        'topLeft' => $activeSop === 0 ? 8 : 0,
                        'bottomLeft' => $activeSop === 0 ? 8 : 0,
                        'topRight' => 8,
                        'bottomRight' => 8,
                    ],
                    'borderSkipped' => false,
                    'barPercentage' => 0.6,
                    'categoryPercentage' => 1,
                ],
            ],
            'labels' => ["Total: {$totalSop} SOP"],
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
                    'align' => 'center',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'rectRounded',
                        'padding' => 25,
                        'font' => [
                            'size' => 13,
                            'weight' => '600',
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
                    'display' => true,
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(156, 163, 175, 0.1)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'font' => [
                            'size' => 11,
                        ],
                        'color' => 'rgba(107, 114, 128, 0.7)',
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'display' => true,
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 13,
                            'weight' => '600',
                        ],
                        'color' => 'rgba(55, 65, 81, 0.9)',
                        'padding' => 10,
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
            ],
            'layout' => [
                'padding' => [
                    'top' => 5,
                    'right' => 20,
                    'bottom' => 5,
                    'left' => 10,
                ],
            ],
            'animation' => [
                'duration' => 800,
                'easing' => 'easeOutQuart',
            ],
        ];
    }
}
