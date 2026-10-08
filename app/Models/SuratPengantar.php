<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPengantar extends Model
{
    /** @use HasFactory<\Database\Factories\SuratPengantarFactory> */
    use HasFactory;

    protected $table = 'surat_pengantar';

    protected $fillable = [
        'pengajuan_id',
        'nomor_surat',
        'tanggal_surat',
        'file_path',
        'verification_token',
        'generated_at',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'generated_at' => 'datetime',
        ];
    }

    // ==================== Relationships ====================

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // ==================== Helpers ====================

    public function verificationUrl(): string
    {
        return route('verifikasi.surat', ['token' => $this->verification_token]);
    }
}
