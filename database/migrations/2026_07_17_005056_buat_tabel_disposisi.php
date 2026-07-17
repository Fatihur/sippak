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
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pengaduan::class, 'pengaduan_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_disposisi')->unique();
            $table->foreignIdFor(User::class, 'dari_user_id')->constrained('users');
            $table->foreignIdFor(User::class, 'untuk_user_id')->nullable()->constrained('users');
            $table->string('tingkat'); // kadis / kabid
            $table->date('tanggal_disposisi');
            $table->string('prioritas')->nullable(); // biasa / penting / sangat_mendesak
            $table->text('instruksi')->nullable();
            $table->text('arahan_pelaksanaan')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposisi');
    }
};
