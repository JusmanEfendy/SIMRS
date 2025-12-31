<?php

namespace App\Filament\Widgets;

use App\Models\Story;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoryStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [];
    }
}
