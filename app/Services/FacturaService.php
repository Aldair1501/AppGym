<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Pago;

class FacturaService
{
    public function crearDesdePago(Pago $pago): Factura
    {
        return Factura::create([
            'pago_id' => $pago->id,
            'numero_factura' => $this->generarNumeroFactura(),
            'fecha_emision' => now(),
            'total' => $pago->monto,
        ]);
    }

    protected function generarNumeroFactura(): string
    {
        $ultimoId = Factura::max('id') + 1;
        return 'FAC-' . str_pad($ultimoId, 6, '0', STR_PAD_LEFT);
    }
}
