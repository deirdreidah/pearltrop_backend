<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $response = (new \App\Services\RoleService())->create($data);
        if (!$response->success) {
            \Filament\Notifications\Notification::make()
                ->title('Error creating role')
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
