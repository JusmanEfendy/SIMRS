<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class UsersOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected static ?int $sort = 1;

    /**
     * Only show for Admin role.
     */
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Admin');
    }

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $adminCount = User::role('Admin')->count();
        $direksiCount = User::role('Direksi')->count();
        $verifikatorCount = User::role('Verifikator')->count();
        $unitCount = User::role('Unit')->count();
        
        // Users created this month
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Semua pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, $totalUsers])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Admin', $adminCount)
                ->description('Administrator sistem')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),

            Stat::make('Direksi', $direksiCount)
                ->description('Manajemen direktur')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),

            Stat::make('Verifikator', $verifikatorCount)
                ->description('Verifikator SOP')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Unit', $unitCount)
                ->description('Staff unit kerja')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),

            Stat::make('User Baru', $newUsersThisMonth)
                ->description('Bulan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('gray'),
        ];
    }
}
