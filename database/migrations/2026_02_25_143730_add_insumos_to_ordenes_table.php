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
        Schema::table('ordenes', function (Blueprint $table) {
            $table->decimal('costo_insumos', 10, 2)->default(0)->after('total');
            $table->text('notas_insumos')->nullable()->after('costo_insumos');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn(['costo_insumos', 'notas_insumos']);
        });
    }
};
