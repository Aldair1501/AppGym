<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asistencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asistencia';

    protected $fillable = [
        'cliente_id',
        'fecha_hora_entrada',
        'fecha_hora_salida',
    ];

    protected $dates = [
        'fecha_hora_entrada',
        'fecha_hora_salida',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relación: una asistencia pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
