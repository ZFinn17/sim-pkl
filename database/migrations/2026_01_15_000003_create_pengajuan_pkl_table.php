<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_pkl', function (Blueprint $table) {
            $table->id();

            $table->string('kode_pengajuan', 30)->unique();
            $table->enum('jenis_pengajuan', ['individual', 'group'])->default('individual');

            $table->foreignId('ketua_siswa_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('perusahaan_id')
                ->constrained('perusahaan')
                ->restrictOnDelete();

            $table->foreignId('guru_pembimbing_id')
                ->nullable()
                ->constrained('guru_pembimbing')
                ->nullOnDelete();

            $table->date('tgl_pengajuan');
            $table->date('tgl_mulai_pkl');
            $table->date('tgl_selesai_pkl');

            $table->enum('status', [
                'submitted',
                'hubin_approved',
                'waiting_company',
                'accepted',
                'rejected',
                'cancelled',
            ])->default('submitted');

            $table->text('alasan_penolakan')->nullable();
            $table->text('catatan_hubin')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index('kode_pengajuan');
            $table->index('status');
            $table->index('perusahaan_id');
            $table->index('ketua_siswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pkl');
    }
};
