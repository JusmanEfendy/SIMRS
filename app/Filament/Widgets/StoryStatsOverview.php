<?php

namespace App\Filament\Widgets;

use App\Models\Story;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoryStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // menghitung total story berdasarkan status
            Stat::make('Waiting for Review', Story::where('status', 'waiting for review')
                ->when(auth()->user()->hasRole('Writer'), function ($query) {
                    $query->where('author_id', auth()->user()->id);
                })->count()),


            Stat::make('In Review', Story::where('status', 'in review')
                ->when(auth()->user()->hasRole('Writer'), function ($query) {
                    $query->where('author_id', auth()->user()->id);
                })->count()),


            Stat::make('Approved', Story::where('status', 'approved')
                ->when(auth()->user()->hasRole('Writer'), function ($query) {
                    $query->where('author_id', auth()->user()->id);
                })->count()),


            Stat::make('Canceled', Story::where('status', 'canceled')
                ->when(auth()->user()->hasRole('Writer'), function ($query) {
                    $query->where('author_id', auth()->user()->id);
                })->count()),


            Stat::make('Rework', Story::where('status', 'rework')
                ->when(auth()->user()->hasRole('Writer'), function ($query) {
                    $query->where('author_id', auth()->user()->id);
                })->count()),
        ];
    }
}
