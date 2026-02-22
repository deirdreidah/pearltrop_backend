<?php

namespace App\Filament\Widgets;

use App\Models\Car;
use App\Models\CarBooking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Revenue', '$' . number_format(CarBooking::where('status', 'confirmed')->orWhere('status', 'completed')->sum('total_price'), 2))
                ->description('Total revenue from confirmed/completed bookings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Active Bookings', CarBooking::where('status', 'confirmed')->count())
                ->description('Current active car bookings')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Available Cars', Car::where('is_available', true)->count())
                ->description('Cars currently available for rent')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),
            Stat::make('Pending Bookings', CarBooking::where('status', 'pending')->count())
                ->description('Bookings waiting for approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
