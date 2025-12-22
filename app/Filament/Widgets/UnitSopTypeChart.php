<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;

class UnitSopTypeChart extends ChartWidget
{
    protected static ?string $heading = '📈 Distribusi Tipe SOP';
    
    protected static ?string $description = 'Perbandingan tipe SOP di unit Anda';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 1;

    /**
     * Only show for Unit role.
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Unit');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $unitId = $user->id_unit;

        if (!$unitId) {
            return [
                'datasets' => [
                    [
                        'data' => [0, 0],
                        'backgroundColor' => ['#6366f1', '#8b5cf6'],
                    ],
                ],
                'labels' => ['Internal', 'AP'],
            ];
        }

        // Count SOPs by type
        $internalSop = Sop::where('id_unit', $unitId)->where('type_sop', 'Internal')->count();
        $apSop = Sop::where('id_unit', $unitId)->where('type_sop', 'AP')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tipe SOP',
                    'data' => [$internalSop, $apSop],
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.8)',   // indigo - internal
                        'rgba(139, 92, 246, 0.8)',  // violet - AP
                    ],
                    'borderColor' => [
                        'rgb(99, 102, 241)',
                        'rgb(139, 92, 246)',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 10,
                ],
            ],
            'labels' => ['🏠 Internal', '🤝 Antar Profesi (AP)'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                    'bodyFont' => [
                        'size' => 13,
                    ],
                    'padding' => 12,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                    'boxPadding' => 6,
                ],
            ],
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
            ],
        ];
    }
}
