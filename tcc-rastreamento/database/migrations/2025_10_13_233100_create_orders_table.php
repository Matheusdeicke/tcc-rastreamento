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
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('requester_id')->constrained('users'); // quem solicitou o kit
            $t->foreignId('handler_id')->nullable()->constrained('users'); // quem aceitou/preparou na CME
            $t->enum('status', [
                'solicitado',
                'aceito',
                'em_preparo',
                'pronto_para_envio',
                'entregue',
                'fechado',
                'recusado',
                'cancelado'
            ])->default('solicitado');
            $t->timestamp('needed_at')->nullable();   // quando o solicitante precisa do kit
            $t->string('setor')->nullable();          // ex: Centro Cirúrgico Sala 2
            $t->string('paciente')->nullable();       // opcional
            $t->text('observacoes')->nullable();
            $t->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
