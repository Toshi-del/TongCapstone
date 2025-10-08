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
        Schema::create('pre_employment_records', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->string('email');
            $table->string('phone_number');
            $table->text('address')->nullable();
            $table->foreignId('medical_test_categories_id')->constrained('medical_test_categories');
            $table->unsignedBigInteger('medical_test_id')->nullable();
            $table->text('other_exams')->nullable();
            $table->enum('billing_type', ['Patient', 'Company']);
            $table->string('company_name')->nullable();
            $table->string('uploaded_file')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_employment_records');
    }
};
