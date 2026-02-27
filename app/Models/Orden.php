<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orden extends Model
{

    use SoftDeletes;
    protected $casts = [
    'fecha' => 'date:Y-m-d',
    'total' => 'decimal:2'
    ];
    protected $table = 'ordenes';
    protected $fillable = ['cliente_id', 'vehiculo_id', 'fecha', 'total', 'estado','costo_insumos','notas_insumos'];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class)->withTrashed();
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class)
                    ->withPivot('precio')
                    ->withTimestamps();
    } 
    // ... otros métodos ...

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'orden_producto')
                    ->withPivot('cantidad', 'precio_unitario', 'cobrar') // Añadir 'cobrar'
                    ->withTimestamps();
    }


    
    
    
}
