<?php

namespace App\Notifications;

use App\Models\GuruPembimbing;
use App\Models\PengajuanPkl;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanDisetujuiHubin extends Notification
{
    use Queueable;

    public function __construct(
        private readonly PengajuanPkl $pengajuan,
        private readonly GuruPembimbing $guru,
        private readonly string $nomorSurat,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan Disetujui Hubin',
            'message' => "Pengajuan Anda ({$this->pengajuan->kode_pengajuan}) telah disetujui Hubin. "
                . "Guru pembimbing: {$this->guru->nama_lengkap}. Surat pengantar {$this->nomorSurat} telah dibuat.",
            'url' => route('student.pengajuan.show', $this->pengajuan),
        ];
    }
}
