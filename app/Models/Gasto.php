<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use SoftDeletes;
    protected $table = 'gasto';

    protected $fillable = [
        'concepto',
        'monto',
        'fecha',
        'tipo',
    ];

    // Si deseas definir los valores posibles del campo enum "tipo"
    const TIPOS = [
        'servicio',
        'salario',
        'mantenimiento',
        'otro',
    ];
}
