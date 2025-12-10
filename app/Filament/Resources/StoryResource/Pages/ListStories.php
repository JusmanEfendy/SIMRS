<?php

namespace App\Filament\Resources\StoryResource\Pages;

use App\Filament\Resources\StoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class ListStories extends ListRecords
{
    protected static string $resource = StoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'waiting for review' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'waiting for review')),
            'in review' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'in review')),
            'approved' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved')),
            'canceled' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'canceled')),
            'rework' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rework')),
        ];
    }
}
