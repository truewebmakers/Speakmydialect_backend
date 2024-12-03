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
            $table->string('job_title')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('translator_id');
            $table->foreign('translator_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('payment_type',['fix','hourly'])->default('fix');
            $table->unsignedInteger('present_rate')->default(0);
            $table->string('day')->nullable();

            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->enum('availability',['phone','video-call','in-person'])->default('in-person');
            $table->enum('status',['accept','reject','cancel','in-process','mark-completed','approved','reject','disputed','pending'])->default('pending');
            // $table->enum('work_status',['approved','reject','disputed','pending','cancel'])->default('pending');
            $table->enum('payment_status',['paid','escrow','hold','dispute','none','failed'])->default('none');

            $table->json('duration')->nullable();
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
