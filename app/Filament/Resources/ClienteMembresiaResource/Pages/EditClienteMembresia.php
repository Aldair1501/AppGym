<?php

namespace App\Filament\Resources\ClienteMembresiaResource\Pages;

use App\Filament\Resources\ClienteMembresiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClienteMembresia extends EditRecord
{
    protected static string $resource = ClienteMembresiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Redirige a la lista de asignacion de membresia después de guardar
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Asignación actualizado correctamente.';
    }
}
