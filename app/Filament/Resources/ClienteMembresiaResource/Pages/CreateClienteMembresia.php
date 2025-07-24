<?php

namespace App\Filament\Resources\ClienteMembresiaResource\Pages;

use App\Filament\Resources\ClienteMembresiaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateClienteMembresia extends CreateRecord
{
    protected static string $resource = ClienteMembresiaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

      protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Membresía asignada correctamente')
            ->success();
    }
}
