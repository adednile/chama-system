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
        Schema::create('chamas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('currency', 3)->default('KES');
            $table->decimal('contribution_target', 10, 2)->default(0.00);
            $table->date('collection_cutoff')->nullable();
            $table->decimal('late_penalty_flat', 10, 2)->default(0.00);
            $table->decimal('interest_rate_pct', 5, 2)->default(0.00);
            $table->decimal('min_credit_score', 3, 1)->default(1.0);
            $table->decimal('savings_weight', 3, 2)->default(0.40);
            $table->decimal('attendance_weight', 3, 2)->default(0.20);
            $table->decimal('repayment_weight', 3, 2)->default(0.40);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chamas');
    }
};
