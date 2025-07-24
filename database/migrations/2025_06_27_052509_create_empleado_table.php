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
         Schema::create('empleado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('puesto', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->date('fecha_contratacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado');
    }
};
