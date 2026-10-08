<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratBalasan extends Model
{
    /** @use HasFactory<\Database\Factories\SuratBalasanFactory> */
    use HasFactory;

    protected $table = 'surat_balasan';

    protected $fillable = [
        'pengajuan_id',
        'uploaded_by',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    // ==================== Relationships ====================

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPkl::class, 'pengajuan_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==================== Helpers ====================

    public function fileSizeReadable(): string
    {
        $bytes = $this->file_size;

        return match (true) {
            $bytes >= 1048576 => round($bytes / 1048576, 2) . ' MB',
            $bytes >= 1024 => round($bytes / 1024, 2) . ' KB',
            default => $bytes . ' B',
        };
    }
}
