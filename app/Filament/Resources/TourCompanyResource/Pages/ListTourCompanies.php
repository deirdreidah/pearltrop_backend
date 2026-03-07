<?php

namespace App\Filament\Resources\TourCompanyResource\Pages;

use App\Filament\Resources\TourCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTourCompanies extends ListRecords
{
    protected static string $resource = TourCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
