<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mapped_mpesa_transactions', function (Blueprint $table) {
            $table->foreignId('fine_id')
                ->nullable()
                ->constrained('fines')
                ->nullOnDelete()
                ->after('loan_id');
        });
    }

    public function down(): void
    {
        Schema::table('mapped_mpesa_transactions', function (Blueprint $table) {
            $table->dropForeign(['fine_id']);
            $table->dropColumn('fine_id');
        });
    }
};
