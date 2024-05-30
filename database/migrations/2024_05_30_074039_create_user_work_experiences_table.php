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
        Schema::create('user_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('company_name')->nullable();
            $table->string('location')->nullable();
            $table->string('location_type')->nullable();
            $table->string('start_month')->nullable();
            $table->string('start_year')->nullable();
            $table->enum('present_working',['0','1'])->comment('0 for not present, 1 yes presently working here ');
            $table->string('end_month')->nullable();
            $table->string('end_year')->nullable();
            $table->text('job_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_work_experiences');
    }
};
