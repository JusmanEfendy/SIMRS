<?php

namespace App\Filament\Widgets;

use App\Models\Directorate;
use App\Models\Sop;
use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class SopStatusChart extends ChartWidget
{
    protected static ?string $heading = '📊 Statistik Unit & SOP per Direktorat';

    protected static ?string $description = 'Perbandingan jumlah Unit dan SOP di setiap Direktorat';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

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
        // Get all directorates with their unit and SOP counts
        $directorates = Directorate::withCount('units')
            ->get()
            ->map(function ($directorate) {
                $unitIds = Unit::where('dir_id', $directorate->id)->pluck('id_unit');
                $sopCount = Sop::whereIn('id_unit', $unitIds)->count();
                
                return [
                    'name' => $directorate->dir_name,
                    'units_count' => $directorate->units_count,
                    'sop_count' => $sopCount,
                ];
            });

        if ($directorates->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Unit',
                        'data' => [0],
                        'backgroundColor' => 'rgba(156, 163, 175, 0.5)',
                    ],
                ],
                'labels' => ['Tidak ada data'],
            ];
        }

        $labels = $directorates->pluck('name')->toArray();
        $unitCounts = $directorates->pluck('units_count')->toArray();
        $sopCounts = $directorates->pluck('sop_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => '🏢 Jumlah Unit',
                    'data' => $unitCounts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.85)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 0,
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                ],
                [
                    'label' => '📄 Jumlah SOP',
                    'data' => $sopCounts,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.85)',
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'borderWidth' => 0,
                    'borderRadius' => 6,
                    'borderSkipped' => false,
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
                    'display' => true,
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                            'weight' => '500',
                        ],
                        'color' => 'rgba(55, 65, 81, 0.9)',
                        'maxRotation' => 45,
                        'minRotation' => 0,
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
                'y' => [
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
                'duration' => 800,
                'easing' => 'easeOutQuart',
            ],
        ];
    }
}
