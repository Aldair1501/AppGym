<?php

namespace App\Filament\Resources\PagoResource\Pages;

use App\Filament\Resources\PagoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\FacturaService;
use Illuminate\Support\Facades\Log;



class CreatePago extends CreateRecord
{
    protected static string $resource = PagoResource::class;
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pago actualizado correctamente.';
    }

   protected function afterCreate(): void
{
    try {
        app(FacturaService::class)->crearDesdePago($this->record);
    } catch (\Throwable $e) {
        Log::error('Error creando factura automática: ' . $e->getMessage());
    }
}
}
