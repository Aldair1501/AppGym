<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{

    use SoftDeletes;
    protected $table = 'factura';

    protected $fillable = [
        'pago_id',
        'numero_factura',
        'fecha_emision',
        'total',
    ];

    // Relación con Pago
    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }
}