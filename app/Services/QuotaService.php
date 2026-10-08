<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuotaService
{
    /**
     * Perusahaan menerima pengajuan. Kuota HANYA berkurang di sini.
     * Menggunakan row lock pada perusahaan agar aman dari race condition
     * jika dua pengajuan diterima nyaris bersamaan.
     *
     * @throws ValidationException
     */
    public function acceptApplication(PengajuanPkl $pengajuan, User $penerima): PengajuanPkl
    {
        return DB::transaction(function () use ($pengajuan, $penerima) {
            /** @var PengajuanPkl $pengajuan */
            $pengajuan = PengajuanPkl::query()->whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();

            if ($pengajuan->status !== PengajuanPkl::STATUS_WAITING_COMPANY) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Pengajuan ini tidak lagi berstatus menunggu keputusan perusahaan.',
                ]);
            }

            /** @var Perusahaan $perusahaan */
            $perusahaan = Perusahaan::query()->whereKey($pengajuan->perusahaan_id)->lockForUpdate()->firstOrFail();

            $jumlahPeserta = $pengajuan->jumlahPeserta();

            if (! $perusahaan->masihAdaKuota($jumlahPeserta)) {
                throw ValidationException::withMessages([
                    'pengajuan' => "Kuota tidak mencukupi. Tersedia {$perusahaan->kuota_tersedia}, dibutuhkan {$jumlahPeserta}.",
                ]);
            }

            $kuotaSebelum = $perusahaan->kuota_terisi;
            $perusahaan->kuota_terisi += $jumlahPeserta;
            $perusahaan->save();

            $pengajuan->update([
                'status' => PengajuanPkl::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);

            AuditLog::record(
                userId: $penerima->id,
                action: 'accept',
                module: 'pengajuan',
                description: "Perusahaan {$perusahaan->nama_perusahaan} menerima pengajuan {$pengajuan->kode_pengajuan} ({$jumlahPeserta} peserta).",
                targetType: PengajuanPkl::class,
                targetId: $pengajuan->id,
                oldValues: ['kuota_terisi' => $kuotaSebelum],
                newValues: ['kuota_terisi' => $perusahaan->kuota_terisi],
            );

            $pengajuan = $pengajuan->fresh(['perusahaan', 'ketua']);
            $pengajuan->ketua->notify(new \App\Notifications\PengajuanDiterimaPerusahaan($pengajuan));

            return $pengajuan;
        });
    }

    /**
     * Perusahaan menolak pengajuan. Kuota TIDAK berubah karena belum pernah dikurangi.
     * Alasan penolakan wajib diisi.
     */
    public function rejectApplication(PengajuanPkl $pengajuan, User $penolak, string $alasan): PengajuanPkl
    {
        return DB::transaction(function () use ($pengajuan, $penolak, $alasan) {
            /** @var PengajuanPkl $pengajuan */
            $pengajuan = PengajuanPkl::query()->whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();

            if ($pengajuan->status !== PengajuanPkl::STATUS_WAITING_COMPANY) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Pengajuan ini tidak lagi berstatus menunggu keputusan perusahaan.',
                ]);
            }

            $pengajuan->update([
                'status' => PengajuanPkl::STATUS_REJECTED,
                'alasan_penolakan' => $alasan,
                'rejected_at' => now(),
            ]);

            AuditLog::record(
                userId: $penolak->id,
                action: 'reject',
                module: 'pengajuan',
                description: "Perusahaan menolak pengajuan {$pengajuan->kode_pengajuan}. Alasan: {$alasan}",
                targetType: PengajuanPkl::class,
                targetId: $pengajuan->id,
            );

            $pengajuan = $pengajuan->fresh(['perusahaan', 'ketua']);
            $pengajuan->ketua->notify(new \App\Notifications\PengajuanDitolak($pengajuan, \App\Notifications\PengajuanDitolak::OLEH_PERUSAHAAN));

            return $pengajuan;
        });
    }

    /**
     * Pembatalan pengajuan yang SUDAH accepted, hanya boleh dilakukan Admin.
     * Kuota yang sebelumnya terpakai dikembalikan.
     */
    public function cancelAcceptedByAdmin(PengajuanPkl $pengajuan, User $admin): PengajuanPkl
    {
        return DB::transaction(function () use ($pengajuan, $admin) {
            /** @var PengajuanPkl $pengajuan */
            $pengajuan = PengajuanPkl::query()->whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();

            if ($pengajuan->status !== PengajuanPkl::STATUS_ACCEPTED) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Hanya pengajuan berstatus diterima yang dapat dibatalkan melalui alur ini.',
                ]);
            }

            /** @var Perusahaan $perusahaan */
            $perusahaan = Perusahaan::query()->whereKey($pengajuan->perusahaan_id)->lockForUpdate()->firstOrFail();

            $jumlahPeserta = $pengajuan->jumlahPeserta();
            $kuotaSebelum = $perusahaan->kuota_terisi;
            $perusahaan->kuota_terisi = max(0, $perusahaan->kuota_terisi - $jumlahPeserta);
            $perusahaan->save();

            $pengajuan->update([
                'status' => PengajuanPkl::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            AuditLog::record(
                userId: $admin->id,
                action: 'cancel_accepted',
                module: 'pengajuan',
                description: "Admin membatalkan pengajuan {$pengajuan->kode_pengajuan} yang sudah diterima. Kuota {$perusahaan->nama_perusahaan} dikembalikan.",
                targetType: PengajuanPkl::class,
                targetId: $pengajuan->id,
                oldValues: ['kuota_terisi' => $kuotaSebelum],
                newValues: ['kuota_terisi' => $perusahaan->kuota_terisi],
            );

            return $pengajuan->fresh(['perusahaan', 'ketua']);
        });
    }
}
