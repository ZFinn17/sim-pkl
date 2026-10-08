<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuruPembimbing extends Model
{
    /** @use HasFactory<\Database\Factories\GuruPembimbingFactory> */
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected $table = 'guru_pembimbing';

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'no_hp',
        'email',
        'status',
    ];

    // ==================== Relationships ====================

    public function pengajuanPkl(): HasMany
    {
        return $this->hasMany(PengajuanPkl::class, 'guru_pembimbing_id');
    }

    // ==================== Helpers ====================

    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    // ==================== Query Scopes ====================

    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }
}
