<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_check_items', function (Blueprint $table) {
            $table->id();

            // devolução CME está ligada à tabela 'returns'
            $table->foreignId('return_request_id')
                ->constrained('returns')
                ->cascadeOnDelete();

            $table->foreignId('kit_item_id')
                ->constrained('kit_items')
                ->cascadeOnDelete();

            $table->unsignedInteger('expected_qty');   // quantidade que o kit deveria ter
            $table->unsignedInteger('returned_qty');   // quanto CME conferiu
            $table->unsignedInteger('missing_qty');    // expected - returned (>= 0)

            $table->enum('status', ['ok','faltando','danificado'])
                ->default('ok');

            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_check_items');
    }
};
?>