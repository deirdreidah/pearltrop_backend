<?php

namespace App\Filament\Resources\TourCompanyResource\Pages;

use App\Filament\Resources\TourCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourCompany extends EditRecord
{
    protected static string $resource = TourCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->action(function (\App\Models\TourCompany $record) {
                    $response = (new \App\Services\TourCompanyService())->delete($record);
                    if (!$response->success) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error deleting tour company')
                            ->body($response->message)
                            ->danger()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Tour company deleted successfully')
                            ->success()
                            ->send();
                        $this->redirect($this->getResource()::getUrl('index'));
                    }
                }),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $response = (new \App\Services\TourCompanyService())->update($record, $data);
        if (!$response->success) {
            \Filament\Notifications\Notification::make()
                ->title('Error updating tour company')
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
