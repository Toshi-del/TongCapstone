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
        Schema::table('pre_employment_records', function (Blueprint $table) {
            $table->boolean('age_adjusted')->default(false)->after('total_price');
            $table->decimal('original_price', 10, 2)->nullable()->after('age_adjusted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_employment_records', function (Blueprint $table) {
            $table->dropColumn(['age_adjusted', 'original_price']);
        });
    }
};
