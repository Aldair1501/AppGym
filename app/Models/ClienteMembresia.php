<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteMembresia extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cliente_membresia'; // Nombre de la tabla

    protected $fillable = [
        'cliente_id',
        'membresia_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function membresia()
    {
        return $this->belongsTo(Membresia::class);
    }
}
