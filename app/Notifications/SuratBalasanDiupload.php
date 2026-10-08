<?php

namespace App\Notifications;

use App\Models\PengajuanPkl;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SuratBalasanDiupload extends Notification
{
    use Queueable;

    public function __construct(private readonly PengajuanPkl $pengajuan)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Surat Balasan Terupload',
            'message' => "Surat balasan perusahaan untuk pengajuan {$this->pengajuan->kode_pengajuan} berhasil diupload.",
            'url' => route('student.pengajuan.show', $this->pengajuan),
        ];
    }
}
