<?php

namespace App\Filament\Resources\TourCompanyResource\Pages;

use App\Filament\Resources\TourCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTourCompany extends ViewRecord
{
    protected static string $resource = TourCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
