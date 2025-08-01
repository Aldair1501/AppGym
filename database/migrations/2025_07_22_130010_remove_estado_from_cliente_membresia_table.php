<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void {
    Schema::table('cliente_membresia', function (Blueprint $table) {
        $table->dropColumn('estado');
    });
}
public function down(): void {
    Schema::table('cliente_membresia', function (Blueprint $table) {
        $table->string('estado')->nullable();
    });
}
};
