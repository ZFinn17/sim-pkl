<?php

namespace App\Notifications;

use App\Models\PengajuanPkl;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanDiterimaPerusahaan extends Notification
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
            'title' => 'Pengajuan Diterima',
            'message' => "Selamat! {$this->pengajuan->perusahaan->nama_perusahaan} menerima pengajuan PKL Anda ({$this->pengajuan->kode_pengajuan}).",
            'url' => route('student.pengajuan.show', $this->pengajuan),
        ];
    }
}
