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
        Schema::table('appointments', function (Blueprint $table) {
            // Add approval audit fields
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->timestamp('declined_at')->nullable()->after('approved_by');
            $table->unsignedBigInteger('declined_by')->nullable()->after('declined_at');
            $table->text('decline_reason')->nullable()->after('declined_by');
            
            // Add foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('declined_by')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better query performance
            $table->index('approved_at');
            $table->index('declined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['declined_by']);
            
            // Drop indexes
            $table->dropIndex(['approved_at']);
            $table->dropIndex(['declined_at']);
            
            // Drop columns
            $table->dropColumn([
                'approved_at',
                'approved_by', 
                'declined_at',
                'declined_by',
                'decline_reason'
            ]);
        });
    }
};
