<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orden_servicio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("orden_id");
            $table->unsignedBigInteger("servicio_id");
            $table->decimal('precio',10,2);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('orden_id')
                  ->references('id')
                  ->on('ordenes')
                  ->onDelete('cascade');
            $table->foreign('servicio_id')
                  ->references('id')
                  ->on('servicios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_servicio');
    }
};
