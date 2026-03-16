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
            Stat::make('Total Revenue', 'UGX ' . number_format(CarBooking::whereIn('status', ['confirmed', 'completed'])->sum('total_price'), 0))
                ->description('Total revenue from car bookings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Car Rentals', CarBooking::where('type', 'rent')->where('status', 'confirmed')->count())
                ->description('Active car rentals')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Ride Bookings', CarBooking::where('type', 'ride')->where('status', 'confirmed')->count())
                ->description('Active ride bookings')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info'),
            Stat::make('Pending Bookings', CarBooking::where('status', 'pending')->count())
                ->description('Bookings waiting for approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
