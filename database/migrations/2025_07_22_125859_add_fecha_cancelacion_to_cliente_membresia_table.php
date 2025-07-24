<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void {
        Schema::table('cliente_membresia', function (Blueprint $table) {
            $table->timestamp('fecha_cancelacion')->nullable()->after('fecha_fin');
        });
    }

    public function down(): void {
        Schema::table('cliente_membresia', function (Blueprint $table) {
            $table->dropColumn('fecha_cancelacion');
        });
    }
};