<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\Unit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DireksiUnitSopTable extends BaseWidget
{
    protected static ?string $heading = 'Detail SOP per Unit';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    /**
     * Only show for Direksi role.
     */
    public static function canView(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('Direksi')
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $user = auth()->user();
                $dirId = $user->dir_id;

                if (!$dirId) {
                    return Unit::query()->whereRaw('1 = 0');
                }

                return Unit::query()
                    ->where('dir_id', $dirId)
                    ->withCount([
                        'sops',
                        'sops as active_sops_count' => function ($query) {
                            $query->where('status', 'Aktif');
                        },
                        'sops as expired_sops_count' => function ($query) {
                            $query->where('status', 'Kadaluarsa');
                        },
                        'sops as expiring_sops_count' => function ($query) {
                            $query->where('status', 'Aktif')
                                ->where('days_left', '<=', 90)
                                ->where('days_left', '>', 0);
                        },
                    ]);
            })
            ->columns([
                Tables\Columns\TextColumn::make('unit_name')
                    ->label('Nama Unit')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office-2')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('unit_head_name')
                    ->label('Kepala Unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sops_count')
                    ->label('Total SOP')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('active_sops_count')
                    ->label('Aktif')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('expired_sops_count')
                    ->label('Kadaluarsa')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('expiring_sops_count')
                    ->label('Segera Kadaluarsa')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->tooltip('SOP yang akan kadaluarsa dalam 90 hari'),
            ])
            ->defaultSort('sops_count', 'desc')
            ->striped()
            ->paginated([5, 10, 25])
            ->emptyStateHeading('Belum ada unit')
            ->emptyStateDescription('Unit akan muncul di sini')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}
