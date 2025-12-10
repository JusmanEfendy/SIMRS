<?php

namespace App\Filament\Resources\StoryResource\Pages;

use App\Filament\Resources\StoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStory extends EditRecord
{
    protected static string $resource = StoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = 'waiting for review';

        // ketika story di edit, set reviewer_id dan status
        // if (auth()->user()->hasRole('Reviewer')) {
        //     $data['reviewer_id'] = auth()->id();
        //     $data['status'] = 'in review';
        // }
        return $data;
    }
}
