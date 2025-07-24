<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cliente';

    protected $fillable = [
        'nombre',
        'apellido',
        'genero',
        'email',
        'telefono',
        'fecha_nacimiento',
        'direccion',
        'estado',
    ];



    // Relación con ClienteMembresia
    public function clienteMembresias()
    {
        return $this->hasMany(ClienteMembresia::class);
    }

  public function getNombreCompletoAttribute(): string
{
    return "{$this->nombre} {$this->apellido}";
}
}

