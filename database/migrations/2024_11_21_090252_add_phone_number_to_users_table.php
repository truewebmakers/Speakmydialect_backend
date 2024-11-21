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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('country_code',length: 8)->nullable()->after('email'); // or after a field you prefer
            $table->string('phone_number',25)->nullable()->after('country_code'); // or after a field you prefer

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('country_code');
            $table->dropColumn('phone_number');
        });
    }
};
