<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

    class Vehiculo extends Model
    {
        use HasFactory, SoftDeletes;

        protected $fillable = [
            'cliente_id',
            'marca',
            'modelo',
            'anio',
            'placa',
            'color'
        ];

        public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class);
    }
    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }
}
