<?php

namespace App\Filament\Pages;

use App\Models\Sop;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class TrashSop extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static ?string $navigationLabel = 'Sampah';

    protected static ?string $title = 'Sampah SOP';

    protected static ?string $slug = 'sampah-sop';

    protected static ?string $navigationGroup = 'Manajemen SOP';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.trash-sop';

    public static function shouldRegisterNavigation(): bool
    {
        // return auth()->user()?->hasAnyRole(['Admin', 'Verifikator']);
        return auth()->user()->hasRole('Verifikator');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sop::onlyTrashed()
            )
            ->columns([
                Tables\Columns\TextColumn::make('id_sop')
                    ->label('ID SOP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('sop_name')
                    ->label('Nama SOP')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('unit.unit_name')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->icon('heroicon-o-building-office-2'),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->icon('heroicon-o-user'),
            ])
            ->defaultSort('deleted_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('id_unit')
                    ->label('Unit Kerja')
                    ->relationship('unit', 'unit_name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-arrow-path')
                    ->modalHeading('Pulihkan SOP')
                    ->modalDescription(fn (Sop $record) => "Anda akan memulihkan SOP:\n\n📄 {$record->sop_name}\n📋 ID: {$record->id_sop}")
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->action(function (Sop $record) {
                        $record->restore();

                        Notification::make()
                            ->success()
                            ->title('SOP Dipulihkan')
                            ->body("SOP \"{$record->sop_name}\" berhasil dipulihkan.")
                            ->send();
                    }),

                Tables\Actions\Action::make('forceDelete')
                    ->label('Hapus Permanen')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->modalHeading('Hapus Permanen')
                    ->modalDescription(fn (Sop $record) => "⚠️ PERINGATAN: Tindakan ini tidak dapat dibatalkan!\n\nAnda akan menghapus permanen SOP:\n📄 {$record->sop_name}\n📋 ID: {$record->id_sop}")
                    ->modalSubmitActionLabel('Ya, Hapus Permanen')
                    ->action(function (Sop $record) {
                        $sopName = $record->sop_name;
                        $record->forceDelete();

                        Notification::make()
                            ->success()
                            ->title('SOP Dihapus Permanen')
                            ->body("SOP \"{$sopName}\" telah dihapus secara permanen.")
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('restoreSelected')
                    ->label('Pulihkan Terpilih')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Pulihkan SOP Terpilih')
                    ->modalDescription('Anda akan memulihkan semua SOP yang dipilih.')
                    ->modalSubmitActionLabel('Ya, Pulihkan Semua')
                    ->action(function ($records) {
                        $count = $records->count();
                        $records->each->restore();

                        Notification::make()
                            ->success()
                            ->title('SOP Dipulihkan')
                            ->body("{$count} SOP berhasil dipulihkan.")
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('forceDeleteSelected')
                    ->label('Hapus Permanen Terpilih')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->modalHeading('Hapus Permanen SOP Terpilih')
                    ->modalDescription('⚠️ PERINGATAN: Tindakan ini tidak dapat dibatalkan! Semua SOP yang dipilih akan dihapus permanen.')
                    ->modalSubmitActionLabel('Ya, Hapus Permanen Semua')
                    ->action(function ($records) {
                        $count = $records->count();
                        $records->each->forceDelete();

                        Notification::make()
                            ->success()
                            ->title('SOP Dihapus Permanen')
                            ->body("{$count} SOP telah dihapus secara permanen.")
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->striped()
            ->emptyStateIcon('heroicon-o-trash')
            ->emptyStateHeading('Sampah Kosong')
            ->emptyStateDescription('Tidak ada dokumen SOP yang dihapus.');
    }
}
