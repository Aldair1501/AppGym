<?php

namespace App\Filament\Resources\MembresiaResource\Pages;

use App\Filament\Resources\MembresiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMembresia extends EditRecord
{
    protected static string $resource = MembresiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

     protected function getRedirectUrl(): string
    {
        // Redirige a la lista de Membresías después de guardar
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Membresía actualizado correctamente.';
    }
}
