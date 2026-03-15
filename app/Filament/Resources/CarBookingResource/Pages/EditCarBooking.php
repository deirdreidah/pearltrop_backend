<?php

namespace App\Filament\Resources\CarBookingResource\Pages;

use App\Filament\Resources\CarBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarBooking extends EditRecord
{
    protected static string $resource = CarBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $response = (new \App\Services\CarBookingService())->update($record, $data);
        if (!$response->success) {
            \Filament\Notifications\Notification::make()
                ->title('Error updating booking')
                ->body($response->message)
                ->danger()
                ->send();
            $this->halt();
        }
        return $response->data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return "Booking #{$this->record->id} has been updated";
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
