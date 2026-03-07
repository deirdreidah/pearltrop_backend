<?php

namespace App\Filament\Resources\TourCompanyResource\Pages;

use App\Filament\Resources\TourCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTourCompany extends CreateRecord
{
    protected static string $resource = TourCompanyResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $response = (new \App\Services\TourCompanyService())->create($data);
        if (!$response->success) {
            \Filament\Notifications\Notification::make()
                ->title('Error creating tour company')
                ->body($response->message)
                ->danger()
                ->send();
            $this->halt();
        }
        return $response->data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
