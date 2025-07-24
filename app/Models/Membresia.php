<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membresia extends Model
{
    use SoftDeletes;
    protected $table = 'membresia';

    protected $fillable = [
        'nombre',
        'tipo',
        'precio',
        'duracion_dias',
        'descripcion',
    ];

    // Si quieres que created_at y updated_at sean tratados como fechas (opcional)
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Relación con ClienteMembresia
    public function clienteMembresias()
    {
        return $this->hasMany(ClienteMembresia::class);
    }
}