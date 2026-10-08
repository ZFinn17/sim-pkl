<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan penanda kapan Admin membuka kembali (reopen) kesempatan
     * siswa untuk mengajukan ulang ke perusahaan yang sebelumnya menolaknya.
     * Lihat business rule: "reapply ke perusahaan sama membutuhkan kontrol Admin".
     */
    public function up(): void
    {
        Schema::table('pengajuan_pkl', function (Blueprint $table) {
            $table->timestamp('reopened_at')->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_pkl', function (Blueprint $table) {
            $table->dropColumn('reopened_at');
        });
    }
};
