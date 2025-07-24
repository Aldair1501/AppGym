<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;
    protected $table = 'pago';

    protected $fillable = [
        'cliente_id',
        'membresia_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
    ];

    // Relación con Cliente
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }


    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class);
    }

    public function membresia(): BelongsTo
{
    return $this->belongsTo(Membresia::class);
}
}