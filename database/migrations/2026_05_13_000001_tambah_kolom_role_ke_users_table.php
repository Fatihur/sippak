<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('operator')->after('password');
            $table->boolean('aktif')->default(true)->after('role');
            $table->timestamp('terakhir_login_at')->nullable()->after('aktif');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'aktif', 'terakhir_login_at']);
        });
    }
};
