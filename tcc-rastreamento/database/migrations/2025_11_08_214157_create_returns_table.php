<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kit_instance_id')->constrained('kit_instances')->cascadeOnDelete();
            $t->foreignId('requested_by_user_id')->constrained('users')->cascadeOnDelete();
            $t->timestamp('requested_at')->useCurrent();
            $t->string('status', 32)->index();
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
