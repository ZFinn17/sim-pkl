<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nama_perusahaan');
            $table->text('alamat_lengkap');
            $table->string('nama_pic');
            $table->string('kontak_hrd', 30);
            $table->string('email_hrd');

            $table->unsignedInteger('kuota_maksimal')->default(0);
            $table->unsignedInteger('kuota_terisi')->default(0);

            $table->enum('status_mitra', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('deskripsi')->nullable();

            $table->timestamps();

            $table->index('status_mitra');
            $table->index('nama_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan');
    }
};
