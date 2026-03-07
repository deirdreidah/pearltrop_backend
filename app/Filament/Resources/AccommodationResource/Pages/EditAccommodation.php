<?php

namespace App\Filament\Resources\AccommodationResource\Pages;

use App\Filament\Resources\AccommodationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccommodation extends EditRecord
{
    protected static string $resource = AccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $response = (new \App\Services\AccommodationService())->update($record, $data);
        if (!$response->success) {
            \Filament\Notifications\Notification::make()
                ->title('Error updating accommodation')
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
