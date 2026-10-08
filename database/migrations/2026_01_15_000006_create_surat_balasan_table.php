<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_balasan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_id')
                ->constrained('pengajuan_pkl')
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('file_path');
            $table->string('original_filename');
            $table->unsignedBigInteger('file_size')->comment('ukuran file dalam bytes');
            $table->string('mime_type', 100);
            $table->timestamp('uploaded_at');
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('pengajuan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_balasan');
    }
};
