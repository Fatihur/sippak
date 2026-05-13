<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_notifikasi_whatsapp', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pengaduan_id')->nullable()->constrained('pengaduan')->nullOnDelete();
            $table->string('nomor_tujuan', 30);
            $table->string('jenis');
            $table->string('status')->default('pending');
            $table->text('pesan');
            $table->json('response')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('terkirim_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_notifikasi_whatsapp');
    }
};
