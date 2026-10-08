<?php

namespace App\Notifications;

use App\Models\PengajuanPkl;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanDikirim extends Notification
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
            'title' => 'Pengajuan Terkirim',
            'message' => "Pengajuan PKL Anda ke {$this->pengajuan->perusahaan->nama_perusahaan} ({$this->pengajuan->kode_pengajuan}) berhasil dikirim.",
            'url' => route('student.pengajuan.show', $this->pengajuan),
        ];
    }
}
