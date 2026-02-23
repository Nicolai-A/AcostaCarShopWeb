<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{   
    use SoftDeletes;
    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'direccion',
    ];

    public function vehiculos()
    {
        return $this->hasMany(\App\Models\Vehiculo::class);
    }
    protected static function booted()
    {
        // Cuando se elimine (soft delete)
        static::deleting(function ($cliente) {

            if ($cliente->isForceDeleting()) {
                // Si es eliminación definitiva
                $cliente->vehiculos()->withTrashed()->forceDelete();
            } else {
                // Si es soft delete
                $cliente->vehiculos()->delete();
            }
        });

        // Cuando se restaure
        static::restoring(function ($cliente) {
            $cliente->vehiculos()->withTrashed()->restore();
        });
    }
    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }
}

