<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kelompok', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_id')
                ->constrained('pengajuan_pkl')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            // Siswa yang sama tidak boleh masuk dua kali dalam satu pengajuan.
            $table->unique(['pengajuan_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kelompok');
    }
};
