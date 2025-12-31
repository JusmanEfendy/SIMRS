<?php

namespace App\Filament\Resources\SopHistoryResource\Pages;

use App\Filament\Resources\SopHistoryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSopHistory extends ViewRecord
{
    protected static string $resource = SopHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
