<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table): void {
            $table->id();
            $table->string('nomor_tiket')->unique()->nullable();
            $table->string('otp_hash')->nullable();
            $table->timestamp('otp_kedaluwarsa_at')->nullable();
            $table->timestamp('terverifikasi_at')->nullable();

            $table->string('nama_pelapor');
            $table->string('nik_pelapor', 32);
            $table->string('jenis_kelamin_pelapor', 20);
            $table->string('nomor_whatsapp', 30);
            $table->string('email_pelapor')->nullable();
            $table->text('alamat_pelapor');
            $table->string('kecamatan')->nullable();

            $table->string('nama_korban');
            $table->unsignedTinyInteger('umur_korban');
            $table->string('jenis_kelamin_korban', 20);
            $table->string('hubungan_dengan_pelapor');

            $table->string('jenis_kekerasan');
            $table->string('lokasi_kejadian');
            $table->date('tanggal_kejadian');
            $table->longText('kronologi_kejadian');

            $table->string('status')->default('menunggu_otp');
            $table->string('tingkat_urgensi')->default('sedang');
            $table->text('catatan_umum')->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
