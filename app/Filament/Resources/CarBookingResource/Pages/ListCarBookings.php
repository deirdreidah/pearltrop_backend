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
}
