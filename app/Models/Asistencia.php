<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asistencia extends Model
{
    use SoftDeletes;

    protected $table = 'asistencia';

    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente_id',
        'fecha_hora_entrada',
    ];

    protected $dates = [
        'fecha_hora_entrada',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relación con Cliente (asumiendo que tienes modelo Cliente)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
