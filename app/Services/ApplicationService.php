<?php

namespace App\Services;

use App\Models\AnggotaKelompok;
use App\Models\AuditLog;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    /**
     * Buat pengajuan PKL baru untuk siswa (ketua), lengkap dengan validasi
     * business rule dan (jika kelompok) anggota-anggotanya.
     *
     * @param  array<int, int>  $anggotaIds  ID siswa anggota kelompok (tidak termasuk ketua)
     *
     * @throws ValidationException
     */
    public function createApplication(
        User $ketua,
        Perusahaan $perusahaan,
        string $jenisPengajuan,
        string $tglMulai,
        string $tglSelesai,
        array $anggotaIds = [],
    ): PengajuanPkl {
        $this->assertKetuaBolehMengajukan($ketua, $perusahaan);

        $anggotaIds = array_values(array_unique($anggotaIds));

        if ($jenisPengajuan === PengajuanPkl::JENIS_GROUP) {
            $this->assertAnggotaValid($ketua, $anggotaIds);
        } else {
            $anggotaIds = [];
        }

        if (! $perusahaan->isAktif()) {
            throw ValidationException::withMessages(['perusahaan' => 'Perusahaan ini sudah tidak aktif menerima pengajuan.']);
        }

        // Catatan: kuota baru benar-benar dikunci & dikurangi saat perusahaan accept
        // (lihat QuotaService). Di titik submit ini kita hanya menolak jika kuota
        // sudah benar-benar 0, supaya siswa tidak mengajukan ke perusahaan yang penuh.
        if ($perusahaan->kuota_tersedia < 1) {
            throw ValidationException::withMessages(['perusahaan' => 'Kuota perusahaan sudah penuh.']);
        }

        return DB::transaction(function () use ($ketua, $perusahaan, $jenisPengajuan, $tglMulai, $tglSelesai, $anggotaIds) {
            $pengajuan = PengajuanPkl::create([
                'kode_pengajuan' => $this->generateKodePengajuan(),
                'jenis_pengajuan' => $jenisPengajuan,
                'ketua_siswa_id' => $ketua->id,
                'perusahaan_id' => $perusahaan->id,
                'tgl_pengajuan' => now()->toDateString(),
                'tgl_mulai_pkl' => $tglMulai,
                'tgl_selesai_pkl' => $tglSelesai,
                'status' => PengajuanPkl::STATUS_SUBMITTED,
            ]);

            foreach ($anggotaIds as $siswaId) {
                AnggotaKelompok::create([
                    'pengajuan_id' => $pengajuan->id,
                    'siswa_id' => $siswaId,
                ]);
            }

            AuditLog::record(
                userId: $ketua->id,
                action: 'submit',
                module: 'pengajuan',
                description: "Siswa {$ketua->nama_lengkap} mengajukan PKL ke {$perusahaan->nama_perusahaan} ({$pengajuan->kode_pengajuan}).",
                targetType: PengajuanPkl::class,
                targetId: $pengajuan->id,
            );

            $pengajuan = $pengajuan->fresh(['anggotaKelompok', 'perusahaan']);
            $ketua->notify(new \App\Notifications\PengajuanDikirim($pengajuan));

            return $pengajuan;
        });
    }

    /**
     * Batalkan pengajuan atas inisiatif siswa sendiri.
     * Hanya boleh pada status submitted/waiting_company (lihat business rule pembatalan).
     *
     * @throws ValidationException
     */
    public function cancelByStudent(PengajuanPkl $pengajuan, User $siswa): void
    {
        if (! $pengajuan->canBeCancelledByStudent()) {
            throw ValidationException::withMessages([
                'pengajuan' => 'Pengajuan yang sudah diterima tidak dapat dibatalkan sendiri. Hubungi Admin Hubin.',
            ]);
        }

        $pengajuan->update([
            'status' => PengajuanPkl::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        AuditLog::record(
            userId: $siswa->id,
            action: 'cancel',
            module: 'pengajuan',
            description: "Siswa {$siswa->nama_lengkap} membatalkan pengajuan {$pengajuan->kode_pengajuan}.",
            targetType: PengajuanPkl::class,
            targetId: $pengajuan->id,
        );
    }

    /**
     * Nomor unik pengajuan, format: PKL-{tahun}-{urutan 4 digit}.
     * Menggunakan lockForUpdate agar aman dari race condition submit bersamaan.
     */
    private function generateKodePengajuan(): string
    {
        $year = now()->year;

        $countThisYear = PengajuanPkl::whereYear('created_at', $year)
            ->lockForUpdate()
            ->count();

        $urutan = str_pad((string) ($countThisYear + 1), 4, '0', STR_PAD_LEFT);

        $kode = "PKL-{$year}-{$urutan}";

        // Jaga-jaga terhadap race condition ekstrem: pastikan benar-benar unique.
        while (PengajuanPkl::where('kode_pengajuan', $kode)->exists()) {
            $urutan = str_pad((string) ((int) $urutan + 1), 4, '0', STR_PAD_LEFT);
            $kode = "PKL-{$year}-{$urutan}";
        }

        return $kode;
    }

    /**
     * @throws ValidationException
     */
    private function assertKetuaBolehMengajukan(User $ketua, Perusahaan $perusahaan): void
    {
        if (! $ketua->is_active) {
            throw ValidationException::withMessages(['siswa' => 'Akun siswa tidak aktif.']);
        }

        $jumlahAktif = PengajuanPkl::query()->milikSiswa($ketua->id)->active()->count();

        if ($jumlahAktif >= PengajuanPkl::MAKSIMAL_PENGAJUAN_AKTIF) {
            throw ValidationException::withMessages([
                'perusahaan' => 'Anda sudah memiliki ' . PengajuanPkl::MAKSIMAL_PENGAJUAN_AKTIF . ' pengajuan aktif.',
            ]);
        }

        $sudahAdaKePerusahaanIni = PengajuanPkl::query()
            ->milikSiswa($ketua->id)
            ->active()
            ->where('perusahaan_id', $perusahaan->id)
            ->exists();

        if ($sudahAdaKePerusahaanIni) {
            throw ValidationException::withMessages([
                'perusahaan' => 'Anda sudah memiliki pengajuan aktif ke perusahaan ini.',
            ]);
        }

        $pengajuanDitolakKePerusahaanIni = PengajuanPkl::query()
            ->where('ketua_siswa_id', $ketua->id)
            ->where('perusahaan_id', $perusahaan->id)
            ->where('status', PengajuanPkl::STATUS_REJECTED)
            ->latest('rejected_at')
            ->first();

        if ($pengajuanDitolakKePerusahaanIni && $pengajuanDitolakKePerusahaanIni->isBlockingReapply()) {
            throw ValidationException::withMessages([
                'perusahaan' => 'Pengajuan Anda ke perusahaan ini sebelumnya ditolak. Hubungi Admin Hubin untuk membuka kembali kesempatan mengajukan ke perusahaan ini.',
            ]);
        }
    }

    /**
     * @param  array<int, int>  $anggotaIds
     *
     * @throws ValidationException
     */
    private function assertAnggotaValid(User $ketua, array $anggotaIds): void
    {
        $totalPeserta = 1 + count($anggotaIds);

        if ($totalPeserta > PengajuanPkl::MAKSIMAL_ANGGOTA_KELOMPOK) {
            throw ValidationException::withMessages([
                'anggota' => 'Maksimal ' . PengajuanPkl::MAKSIMAL_ANGGOTA_KELOMPOK . ' siswa dalam satu kelompok (termasuk ketua).',
            ]);
        }

        if (in_array($ketua->id, $anggotaIds, true)) {
            throw ValidationException::withMessages(['anggota' => 'Ketua tidak perlu dipilih sebagai anggota.']);
        }

        if (count($anggotaIds) !== count(array_unique($anggotaIds))) {
            throw ValidationException::withMessages(['anggota' => 'Tidak boleh memilih siswa yang sama dua kali.']);
        }

        foreach ($anggotaIds as $siswaId) {
            $anggota = User::query()->role(User::ROLE_SISWA)->find($siswaId);

            if (! $anggota || ! $anggota->is_active) {
                throw ValidationException::withMessages(['anggota' => 'Salah satu anggota tidak valid atau tidak aktif.']);
            }

            $jumlahAktifAnggota = PengajuanPkl::query()->milikSiswa($anggota->id)->active()->count();

            if ($jumlahAktifAnggota >= PengajuanPkl::MAKSIMAL_PENGAJUAN_AKTIF) {
                throw ValidationException::withMessages([
                    'anggota' => "{$anggota->nama_lengkap} sudah memiliki " . PengajuanPkl::MAKSIMAL_PENGAJUAN_AKTIF . ' pengajuan aktif.',
                ]);
            }
        }
    }
}
