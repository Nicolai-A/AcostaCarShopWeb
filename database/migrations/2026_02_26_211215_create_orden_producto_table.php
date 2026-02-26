<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orden_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->integer('cantidad')->default(1);
            $table->boolean('cobrar')->default(true); // <--- AQUÍ LA MAGIA: true = se cobra, false = no se cobra
            $table->decimal('precio_unitario', 10, 2); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_producto');
    }
};
