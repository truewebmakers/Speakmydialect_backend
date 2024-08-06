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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->unsignedBigInteger('job_id');
            $table->foreign('job_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->string('stripe_id')->unique();
            $table->integer('amount')->default(0);
            $table->string('currency')->default('usd');
            $table->string('description')->nullable();
            $table->string('receipt_url')->nullable();
            $table->boolean('captured')->default(false);
            $table->boolean('paid')->default(false);
            $table->string('payment_method')->nullable();
            $table->string('payment_method_brand')->nullable();
            $table->string('payment_method_last4')->nullable();
            $table->integer('payment_method_exp_month')->nullable();
            $table->integer('payment_method_exp_year')->nullable();
            $table->string('billing_address_postal_code')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_name')->nullable();
            $table->string('invoice_url')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};

// php artisan migrate:refresh --path=/database/migrations/2024_08_06_064530_create_payouts_table.php
