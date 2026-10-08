<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_pengantar', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_id')
                ->unique()
                ->constrained('pengajuan_pkl')
                ->cascadeOnDelete();

            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->string('file_path');
            $table->string('verification_token', 64)->unique();
            $table->timestamp('generated_at');

            $table->foreignId('generated_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index('nomor_surat');
            $table->index('verification_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengantar');
    }
};
