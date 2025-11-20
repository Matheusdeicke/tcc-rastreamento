<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            if (!Schema::hasColumn('return_items', 'return_id')) {
                $table->unsignedBigInteger('return_id')->nullable()->after('id');
            }
        });

        if (Schema::hasColumn('return_items', 'return_request_id')) {
            DB::statement('UPDATE return_items SET return_id = return_request_id WHERE return_id IS NULL');
        }

        Schema::table('return_items', function (Blueprint $table) {
            $table->index('return_id');

            $table->foreign('return_id')
                  ->references('id')->on('returns')
                  ->onDelete('cascade');
        });

        if (Schema::hasColumn('return_items', 'return_request_id')) {
            Schema::table('return_items', function (Blueprint $table) {
                $table->dropColumn('return_request_id');
            });
        }

        DB::statement('UPDATE return_items SET return_id = return_id WHERE return_id IS NOT NULL'); 
        // Schema::table('return_items', function (Blueprint $table) {
        //     $table->unsignedBigInteger('return_id')->nullable(false)->change();
        // });
    }

    public function down(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            $table->dropForeign(['return_id']);
            $table->dropIndex(['return_id']);
            $table->dropColumn('return_id');
        });
    }
};
