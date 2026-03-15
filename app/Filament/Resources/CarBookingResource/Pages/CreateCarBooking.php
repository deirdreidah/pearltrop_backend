<?php

namespace App\Filament\Resources\CarBookingResource\Pages;

use App\Filament\Resources\CarBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCarBooking extends CreateRecord
{
    protected static string $resource = CarBookingResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $response = (new \App\Services\CarBookingService())->create($data);
        if (!$response->success) {
            \Filament\Notifications\Notification::make()
                ->title('Error creating booking')
                ->body($response->message)
                ->danger()
                ->send();
            $this->halt();
        }
        return $response->data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return "Booking #{$this->record->id} has been created successfully";
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
