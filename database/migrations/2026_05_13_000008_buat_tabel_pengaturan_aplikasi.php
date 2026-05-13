<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_aplikasi', function (Blueprint $table): void {
            $table->id();
            $table->string('kunci')->unique();
            $table->text('nilai')->nullable();
            $table->boolean('rahasia')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_aplikasi');
    }
};
