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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('member')->after('password');
            }

            if (!Schema::hasColumn('users', 'chama_id')) {
                $table->foreignId('chama_id')->nullable()->after('role')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'national_id')) {
                $table->string('national_id')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('national_id');
            }

            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('phone');
            }

            if (!Schema::hasColumn('users', 'account_status')) {
                $table->string('account_status')->default('active')->after('is_verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'chama_id')) {
                $table->dropConstrainedForeignId('chama_id');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'national_id')) {
                $table->dropColumn('national_id');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('users', 'is_verified')) {
                $table->dropColumn('is_verified');
            }

            if (Schema::hasColumn('users', 'account_status')) {
                $table->dropColumn('account_status');
            }
        });
    }
};
