<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanPkl extends Model
{
    /** @use HasFactory<\Database\Factories\PengajuanPklFactory> */
    use HasFactory;

    protected $table = 'pengajuan_pkl';

    // ==================== Status Constants ====================

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_HUBIN_APPROVED = 'hubin_approved';
    public const STATUS_WAITING_COMPANY = 'waiting_company';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    /** Status yang masih dianggap "aktif" (dihitung ke batas maksimal 2 pengajuan aktif). */
    public const ACTIVE_STATUSES = [
        self::STATUS_SUBMITTED,
        self::STATUS_HUBIN_APPROVED,
        self::STATUS_WAITING_COMPANY,
    ];

    public const JENIS_INDIVIDUAL = 'individual';
    public const JENIS_GROUP = 'group';

    public const MAKSIMAL_PENGAJUAN_AKTIF = 2;
    public const MAKSIMAL_ANGGOTA_KELOMPOK = 3;

    /** Label status yang ditampilkan ke siswa (lebih manusiawi dari status internal). */
    public const STATUS_LABELS = [
        self::STATUS_SUBMITTED => 'Pengajuan Dikirim',
        self::STATUS_HUBIN_APPROVED => 'Diproses Hubin',
        self::STATUS_WAITING_COMPANY => 'Menunggu Perusahaan',
        self::STATUS_ACCEPTED => 'Diterima',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $fillable = [
        'kode_pengajuan',
        'jenis_pengajuan',
        'ketua_siswa_id',
        'perusahaan_id',
        'guru_pembimbing_id',
        'tgl_pengajuan',
        'tgl_mulai_pkl',
        'tgl_selesai_pkl',
        'status',
        'alasan_penolakan',
        'catatan_hubin',
        'approved_by',
        'approved_at',
        'accepted_at',
        'rejected_at',
        'cancelled_at',
        'reopened_at',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pengajuan' => 'date',
            'tgl_mulai_pkl' => 'date',
            'tgl_selesai_pkl' => 'date',
            'approved_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'reopened_at' => 'datetime',
        ];
    }

    // ==================== Relationships ====================

    public function ketua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_siswa_id');
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function guruPembimbing(): BelongsTo
    {
        return $this->belongsTo(GuruPembimbing::class, 'guru_pembimbing_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function anggotaKelompok(): HasMany
    {
        return $this->hasMany(AnggotaKelompok::class, 'pengajuan_id');
    }

    public function suratPengantar(): HasOne
    {
        return $this->hasOne(SuratPengantar::class, 'pengajuan_id');
    }

    public function suratBalasan(): HasMany
    {
        return $this->hasMany(SuratBalasan::class, 'pengajuan_id');
    }

    // ==================== Business Helpers ====================

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isGroup(): bool
    {
        return $this->jenis_pengajuan === self::JENIS_GROUP;
    }

    /** Total peserta = 1 ketua + jumlah anggota kelompok. */
    public function jumlahPeserta(): int
    {
        return 1 + $this->anggotaKelompok()->count();
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    /** Siswa hanya boleh membatalkan sendiri pada status submitted / waiting_company. */
    public function canBeCancelledByStudent(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_WAITING_COMPANY], true);
    }

    /**
     * Pengajuan yang ditolak dan belum di-reopen Admin akan memblokir
     * siswa (ketua) untuk mengajukan ulang ke perusahaan yang sama.
     */
    public function isBlockingReapply(): bool
    {
        return $this->status === self::STATUS_REJECTED && $this->reopened_at === null;
    }

    // ==================== Query Scopes ====================

    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Pengajuan di mana siswa berperan sebagai ketua ATAU anggota kelompok.
     * Dibungkus dalam satu grup where() agar aman saat dirangkai dengan
     * scope/where lain (mis. ->milikSiswa($id)->active()) tanpa precedence AND/OR yang salah.
     */
    public function scopeMilikSiswa($query, int $siswaId)
    {
        return $query->where(function ($q) use ($siswaId) {
            $q->where('ketua_siswa_id', $siswaId)
                ->orWhereHas('anggotaKelompok', fn ($qq) => $qq->where('siswa_id', $siswaId));
        });
    }
}
