<?php

namespace App\Filament\Resources\StoryResource\Pages;

use App\Filament\Resources\StoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStory extends ViewRecord
{
    protected static string $resource = StoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            // approve
            Actions\Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Textarea::make('feedback')
                        ->label('Feedback')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull()
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'approved', 'feedback' => $data['feedback']]);
                    // $this->redirect(route('filament.resources.admin.stories.index'));
                    // $this->notify('success', 'Story approved successfully.');
                })
                ->visible(fn() => auth()->user()->hasRole('Reviewer')
                    && $this->record->status === 'in review'
                    && $this->record->reviewer_id === auth()->id()),


            // cancel
            Actions\Action::make('canceled')
                ->label('Cancel')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('feedback')
                        ->label('Feedback')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull()
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'canceled', 'feedback' => $data['feedback']]);
                })
                ->visible(fn() => auth()->user()->hasRole('Reviewer')
                    && $this->record->status === 'in review'
                    && $this->record->reviewer_id === auth()->id()),

            // rework
            Actions\Action::make('rework')
                ->label('Rework')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Textarea::make('feedback')
                        ->label('Feedback')
                        ->rows(3)
                        ->columnSpanFull()
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'rework', 'feedback' => $data['feedback']]);
                })
                ->visible(fn() => auth()->user()->hasRole('Reviewer')
                    && $this->record->status === 'in review'
                    && $this->record->reviewer_id === auth()->id()),
        ];
    }
}
