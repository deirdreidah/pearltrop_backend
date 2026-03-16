<?php

namespace App\Filament\Resources\CarBookingResource\Pages;

use App\Filament\Resources\CarBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarBookings extends ListRecords
{
    protected static string $resource = CarBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Schemas\Components\Tabs\Tab::make('All Bookings'),
            'rent' => \Filament\Schemas\Components\Tabs\Tab::make('Car Rentals')
                ->modifyQueryUsing(fn ($query) => $query->where('type', 'rent')),
            'ride' => \Filament\Schemas\Components\Tabs\Tab::make('Ride Bookings')
                ->modifyQueryUsing(fn ($query) => $query->where('type', 'ride')),
        ];
    }
}
