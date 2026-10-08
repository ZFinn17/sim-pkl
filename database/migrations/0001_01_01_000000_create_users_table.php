<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel users default Laravel diperluas dengan kolom-kolom SIM-PKL:
     * nis_nip, nama_lengkap, kelas, role, no_hp, is_active.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // --- Kolom SIM-PKL ---
            $table->string('nis_nip', 30)->nullable()->comment('NIS untuk siswa, NIP untuk admin jika ada');
            $table->string('nama_lengkap');
            $table->string('kelas', 20)->nullable()->comment('Diisi untuk role siswa, mis. XI RPL 1');
            $table->enum('role', ['siswa', 'admin', 'perusahaan'])->default('siswa');
            $table->string('no_hp', 20)->nullable();
            $table->boolean('is_active')->default(true);

            // --- Kolom default Laravel ---
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('email');
            $table->index('nis_nip');
            $table->index('role');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
