<?php

namespace App\Filament\Resources\ClienteMembresiaResource\Pages;

use App\Filament\Resources\ClienteMembresiaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\ClienteMembresia;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

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

    // Validar antes de crear
   protected function beforeCreate(): void
{
    $data = $this->form->getState();

    $exists = ClienteMembresia::where('cliente_id', $data['cliente_id'])
        ->where('membresia_id', $data['membresia_id'])
        ->where('fecha_inicio', $data['fecha_inicio'])
        ->exists();

    if ($exists) {
        // Mostrar notificación
        Notification::make()
            ->title('Error')
            ->body('Este cliente ya tiene asignada esa membresía para la fecha seleccionada.')
            ->danger()
            ->send();

        // Lanzar excepción para bloquear guardado y mostrar error en campo
        throw ValidationException::withMessages([
            'cliente_id' => ['Este cliente ya tiene asignada esa membresía para la fecha seleccionada.'],
        ]);
    }
}
}

