<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kit_instance_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kit_instance_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // quem registrou
            $t->string('etapa', 100);          // ex: estoque, CME, sala cirúrgica, etc.
            $t->string('local', 150)->nullable(); // localização ou setor
            $t->text('observacoes')->nullable();
            $t->timestamp('registrado_em')->default(now());
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_instance_events');
    }
};
