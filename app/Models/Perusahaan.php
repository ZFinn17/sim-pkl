<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    /** @use HasFactory<\Database\Factories\PerusahaanFactory> */
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected $table = 'perusahaan';

    protected $fillable = [
        'user_id',
        'nama_perusahaan',
        'alamat_lengkap',
        'nama_pic',
        'kontak_hrd',
        'email_hrd',
        'kuota_maksimal',
        'kuota_terisi',
        'status_mitra',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'kuota_maksimal' => 'integer',
            'kuota_terisi' => 'integer',
        ];
    }

    // ==================== Relationships ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuanPkl(): HasMany
    {
        return $this->hasMany(PengajuanPkl::class);
    }

    // ==================== Accessors ====================

    /**
     * kuota_tersedia dihitung dari kuota_maksimal - kuota_terisi,
     * TIDAK disimpan sebagai kolom terpisah agar tidak redundant.
     */
    protected function kuotaTersedia(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->kuota_maksimal - $this->kuota_terisi),
        );
    }

    public function isAktif(): bool
    {
        return $this->status_mitra === self::STATUS_AKTIF;
    }

    public function masihAdaKuota(int $jumlah = 1): bool
    {
        return $this->kuota_tersedia >= $jumlah;
    }

    // ==================== Query Scopes ====================

    public function scopeAktif($query)
    {
        return $query->where('status_mitra', self::STATUS_AKTIF);
    }
}
