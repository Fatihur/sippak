<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asesmen_awal', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pengaduan_id')->unique()->constrained('pengaduan')->cascadeOnDelete();
            $table->text('kondisi_korban');
            $table->string('tingkat_risiko');
            $table->text('kebutuhan_korban')->nullable();
            $table->boolean('pendampingan_hukum')->default(false);
            $table->boolean('pendampingan_psikologis')->default(false);
            $table->text('catatan_operator')->nullable();
            $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asesmen_awal');
    }
};
