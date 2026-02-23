<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'activo'
    ];

    public function ordenes()
    {
        return $this->belongsToMany(Orden::class)
                    ->withPivot('precio')
                    ->withTimestamps();
    }
    
}
