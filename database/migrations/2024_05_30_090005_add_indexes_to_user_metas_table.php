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
        Schema::table('user_metas', function (Blueprint $table) {
            $table->index('phone');
            $table->index('gender');
            $table->index('location');
            $table->index('fix_rate');
            $table->index('hourly_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_metas', function (Blueprint $table) {
            $table->dropIndex('phone');
            $table->dropIndex('gender');
            $table->dropIndex('location');
            $table->dropIndex('fix_rate');
            $table->dropIndex('hourly_rate');
        });
    }
};
