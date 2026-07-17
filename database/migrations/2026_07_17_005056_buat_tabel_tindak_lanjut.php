<?php

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pengaduan::class, 'pengaduan_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->date('tanggal_penanganan');
            $table->text('hasil_penanganan');
            $table->text('keterangan')->nullable();
            $table->string('status_penanganan')->default('diproses'); // diproses / selesai
            $table->string('berita_acara')->nullable();
            $table->string('dokumentasi')->nullable();
            $table->string('dokumen_lain')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
