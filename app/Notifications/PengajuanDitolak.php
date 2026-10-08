<?php

namespace App\Notifications;

use App\Models\PengajuanPkl;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanDitolak extends Notification
{
    use Queueable;

    public const OLEH_HUBIN = 'hubin';
    public const OLEH_PERUSAHAAN = 'perusahaan';

    public function __construct(
        private readonly PengajuanPkl $pengajuan,
        private readonly string $olehSiapa,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $sumber = $this->olehSiapa === self::OLEH_PERUSAHAAN
            ? $this->pengajuan->perusahaan->nama_perusahaan
            : 'Admin Hubin';

        return [
            'title' => 'Pengajuan Ditolak',
            'message' => "Pengajuan PKL Anda ({$this->pengajuan->kode_pengajuan}) ditolak oleh {$sumber}.",
            'url' => route('student.pengajuan.show', $this->pengajuan),
        ];
    }
}
