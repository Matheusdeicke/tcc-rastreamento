<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trace_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kit_instance_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('etapa');           // estoque, preparo, entrega, uso, retorno etc.
            $t->string('local')->nullable();
            $t->text('observacoes')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('trace_events');
    }
};
