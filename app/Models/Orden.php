<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'ordenes';
    protected $fillable = ['cliente_id', 'vehiculo_id', 'fecha', 'total', 'estado'];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class)
                    ->withPivot('precio')
                    ->withTimestamps();
    } 
    
}
