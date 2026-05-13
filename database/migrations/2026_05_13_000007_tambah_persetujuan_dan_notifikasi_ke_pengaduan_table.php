<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table): void {
            $table->boolean('persetujuan_kerahasiaan')->default(true)->after('kronologi_kejadian');
            $table->timestamp('notifikasi_terakhir_at')->nullable()->after('catatan_umum');
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table): void {
            $table->dropColumn(['persetujuan_kerahasiaan', 'notifikasi_terakhir_at']);
        });
    }
};
