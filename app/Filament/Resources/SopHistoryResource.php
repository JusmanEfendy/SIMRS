<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SopHistoryResource\Pages;
use App\Models\SopHistory;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class SopHistoryResource extends Resource
{
    protected static ?string $model = SopHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Riwayat SOP';

    protected static ?string $modelLabel = 'Riwayat SOP';

    protected static ?string $pluralModelLabel = 'Riwayat SOP';

    protected static ?string $navigationGroup = 'Dokumen';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['Direksi', 'Direktorat']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->description(fn ($record) => $record->created_at->diffForHumans()),

                Tables\Columns\TextColumn::make('sop.sop_name')
                    ->label('Nama SOP')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->sop?->sop_name)
                    ->description(fn ($record) => $record->sop?->id_sop),

                Tables\Columns\TextColumn::make('sop.unit.unit_name')
                    ->label('Unit')
                    ->searchable()
                    ->icon('heroicon-o-building-office-2')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->action_label)
                    ->color(fn ($record) => $record->action_color)
                    ->icon(fn ($record) => $record->action_icon),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('changed_fields')
                    ->label('Field Berubah')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) . ' field' : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Tipe Aksi')
                    ->options([
                        'created' => '✅ Dibuat',
                        'updated' => '📝 Diperbarui',
                        'status_changed' => '🔄 Status Berubah',
                        'deleted' => '🗑️ Dihapus',
                        'restored' => '♻️ Dipulihkan',
                    ]),

                Tables\Filters\SelectFilter::make('unit')
                    ->label('Unit')
                    ->relationship('sop.unit', 'unit_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d M Y');
                        }
                        return $indicators;
                    }),
            ])
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([])
            ->striped()
            ->poll('60s')
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading('Belum Ada Riwayat')
            ->emptyStateDescription('Riwayat perubahan SOP akan muncul di sini.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Detail Perubahan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('sop.sop_name')
                                    ->label('Nama SOP')
                                    ->weight(FontWeight::Bold),
                                Components\TextEntry::make('sop.id_sop')
                                    ->label('ID SOP')
                                    ->copyable(),
                                Components\TextEntry::make('sop.unit.unit_name')
                                    ->label('Unit')
                                    ->icon('heroicon-o-building-office'),
                                Components\TextEntry::make('created_at')
                                    ->label('Waktu Perubahan')
                                    ->dateTime('d F Y, H:i:s'),
                            ]),
                    ]),

                Components\Section::make('Aksi')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('action')
                                    ->label('Tipe Aksi')
                                    ->badge()
                                    ->formatStateUsing(fn ($record) => $record->action_label)
                                    ->color(fn ($record) => $record->action_color),
                                Components\TextEntry::make('user.name')
                                    ->label('Dilakukan Oleh')
                                    ->icon('heroicon-o-user'),
                            ]),
                        Components\TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('📝 Detail Perubahan Field')
                    ->description('Perbandingan nilai sebelum dan sesudah perubahan')
                    ->icon('heroicon-o-arrows-right-left')
                    ->schema([
                        Components\ViewEntry::make('changes_display')
                            ->label('')
                            ->view('filament.infolists.components.sop-history-changes')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->changed_fields))
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSopHistories::route('/'),
            'view' => Pages\ViewSopHistory::route('/{record}'),
        ];
    }

    /**
     * Filter history berdasarkan directorate untuk role Direksi.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['sop.unit', 'user']);
        
        // Filter untuk role Direksi/Direktorat
        if (auth()->check() && (auth()->user()->hasRole('Direksi') || auth()->user()->hasRole('Direktorat'))) {
            $userDirId = auth()->user()->dir_id;
            
            if ($userDirId) {
                $query->forDirectorate($userDirId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        
        return $query;
    }

    /**
     * Hanya Direksi yang bisa mengakses menu ini.
     */
    public static function canAccess(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('Direksi') || 
            auth()->user()->hasRole('Direktorat')
        );
    }
}
