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
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreignId('assigned_by')->nullable()->after('user_id')->constrained('users')->onDelete('set null');

            // Update status enum to include pending_product_approval
            $table->enum('status', ['pending', 'pending_product_approval', 'diterima', 'ditolak', 'dikeluarkan'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropColumn('assigned_by');

            // Revert status enum
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'dikeluarkan'])->default('pending')->change();
        });
    }
};
