<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKelompok extends Model
{
    /** @use HasFactory<\Database\Factories\AnggotaKelompokFactory> */
    use HasFactory;

    protected $table = 'anggota_kelompok';

    protected $fillable = [
        'pengajuan_id',
        'siswa_id',
    ];

    // ==================== Relationships ====================

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}
