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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('translator_id');
            $table->foreign('translator_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('payment_type',['fix','hourly'])->default('fix');
            $table->unsignedInteger('present_rate')->default(0);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->enum('availability',['remote','hybrid','onsite'])->default('remote');
            $table->enum('status',['accept','reject','cancle','in-process','mark-completed'])->default('in-process');
            $table->enum('work_status',['approved','reject','disputed','pending','cancle'])->default('pending');
            $table->enum('payment_status',['paid','escrow','hold','dispute','none'])->default('none');
            $table->dateTime('payment_by_client_at')->nullable();
            $table->dateTime('payment_by_translator_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
