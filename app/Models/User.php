<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SISWA = 'siswa';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PERUSAHAAN = 'perusahaan';

    protected $fillable = [
        'nis_nip',
        'nama_lengkap',
        'kelas',
        'role',
        'no_hp',
        'is_active',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ==================== Relationships ====================

    /** Profil perusahaan jika user ini punya role perusahaan. */
    public function perusahaan(): HasOne
    {
        return $this->hasOne(Perusahaan::class);
    }

    /** Pengajuan PKL di mana user ini menjadi ketua/pengaju. */
    public function pengajuanSebagaiKetua(): HasMany
    {
        return $this->hasMany(PengajuanPkl::class, 'ketua_siswa_id');
    }

    /** Pengajuan PKL yang disetujui oleh user ini (biasanya admin). */
    public function pengajuanDisetujui(): HasMany
    {
        return $this->hasMany(PengajuanPkl::class, 'approved_by');
    }

    /** Keanggotaan kelompok PKL yang diikuti user ini sebagai anggota. */
    public function anggotaKelompok(): HasMany
    {
        return $this->hasMany(AnggotaKelompok::class, 'siswa_id');
    }

    /** Surat pengantar yang di-generate oleh user ini (biasanya admin). */
    public function suratPengantarDigenerate(): HasMany
    {
        return $this->hasMany(SuratPengantar::class, 'generated_by');
    }

    /** Surat balasan yang diupload oleh user ini. */
    public function suratBalasanDiupload(): HasMany
    {
        return $this->hasMany(SuratBalasan::class, 'uploaded_by');
    }

    /** Riwayat audit log atas aksi user ini. */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ==================== Role Helpers ====================

    public function isSiswa(): bool
    {
        return $this->role === self::ROLE_SISWA;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPerusahaan(): bool
    {
        return $this->role === self::ROLE_PERUSAHAAN;
    }

    // ==================== Query Scopes ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
