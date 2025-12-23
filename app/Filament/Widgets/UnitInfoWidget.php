<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UnitInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.unit-info-widget';

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';

    /**
     * Only show for Unit role.
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Unit');
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        $unit = $user->unit;

        if (!$unit) {
            return [
                'unit' => null,
                'directorate' => null,
            ];
        }

        return [
            'unit' => $unit,
            'directorate' => $unit->directorate,
        ];
    }
}
