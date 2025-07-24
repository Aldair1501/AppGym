<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;
    
    protected $table = 'empleado';

    protected $fillable = [
        'nombre',
        'puesto',
        'telefono',
        'email',
        'fecha_contratacion',
    ];

    protected $dates = [
        'fecha_contratacion',
    ];
}
