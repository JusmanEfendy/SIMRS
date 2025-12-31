<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;

class UnitSopStatusChart extends ChartWidget
{
    protected static ?string $heading = '📊 Distribusi Status SOP';
    
    protected static ?string $description = 'Perbandingan status SOP di unit Anda';

    protected static ?int $sort = 2;

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
                        'backgroundColor' => ['#10b981', '#ef4444'],
                    ],
                ],
                'labels' => ['Aktif', 'Kadaluarsa'],
            ];
        }

        // Count SOPs by status
        $activeSop = Sop::where('id_unit', $unitId)->where('status', 'Aktif')->count();
        $expiredSop = Sop::where('id_unit', $unitId)->where('status', 'Kadaluarsa')->count();

        // Count SOPs by type
        $internalSop = Sop::where('id_unit', $unitId)->where('type_sop', 'Internal')->count();
        $apSop = Sop::where('id_unit', $unitId)->where('type_sop', 'AP')->count();

        // Expiring soon count
        $expiringSoon = Sop::where('id_unit', $unitId)
            ->where('status', 'Aktif')
            ->where('days_left', '<=', 90)
            ->where('days_left', '>', 0)
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status SOP',
                    'data' => [$activeSop, $expiringSoon, $expiredSop],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)',  // green - aktif
                        'rgba(245, 158, 11, 0.8)',  // amber - segera kadaluarsa
                        'rgba(239, 68, 68, 0.8)',   // red - kadaluarsa
                    ],
                    'borderColor' => [
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 10,
                ],
            ],
            'labels' => ['✅ Aktif', '⏰ Segera Kadaluarsa', '❌ Kadaluarsa'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
            'cutout' => '60%',
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
            ],
        ];
    }
}
