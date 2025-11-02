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
        Schema::create('kit_instances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kit_id')->constrained()->cascadeOnDelete();
            $t->string('etiqueta')->unique();
            $t->string('status')->default('em_estoque'); // ex.: em_estoque, enviado, em_uso...
            $t->timestamp('data_validade')->nullable();
            $t->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kit_instances');
    }
};
