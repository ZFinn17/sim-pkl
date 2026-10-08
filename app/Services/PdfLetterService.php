<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\GuruPembimbing;
use App\Models\PengajuanPkl;
use App\Models\SuratPengantar;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PdfLetterService
{
    private const DISK = 'local';
    private const DIRECTORY = 'surat-pengantar';

    public function __construct(
        private readonly LetterNumberService $letterNumberService,
        private readonly QrVerificationService $qrVerificationService,
    ) {
    }

    /**
     * Validasi, assign guru, terbitkan nomor surat + QR, render PDF, dan
     * lanjutkan status ke waiting_company — semuanya dalam satu transaksi
     * agar tidak ada data setengah jadi jika salah satu langkah gagal.
     *
     * @throws ValidationException
     */
    public function approveAndGenerate(PengajuanPkl $pengajuan, GuruPembimbing $guru, User $admin): PengajuanPkl
    {
        return DB::transaction(function () use ($pengajuan, $guru, $admin) {
            /** @var PengajuanPkl $pengajuan */
            $pengajuan = PengajuanPkl::query()->whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();

            if ($pengajuan->status !== PengajuanPkl::STATUS_SUBMITTED) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Hanya pengajuan berstatus "Pengajuan Dikirim" yang dapat divalidasi.',
                ]);
            }

            if (! $pengajuan->perusahaan->isAktif()) {
                throw ValidationException::withMessages(['pengajuan' => 'Perusahaan tujuan sudah tidak aktif.']);
            }

            if (! $guru->isAktif()) {
                throw ValidationException::withMessages(['guru_pembimbing_id' => 'Guru pembimbing yang dipilih tidak aktif.']);
            }

            $pengajuan->update([
                'status' => PengajuanPkl::STATUS_HUBIN_APPROVED,
                'guru_pembimbing_id' => $guru->id,
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);
            $pengajuan->load(['perusahaan', 'ketua', 'anggotaKelompok.siswa', 'guruPembimbing']);

            $nomorSurat = $this->letterNumberService->next();
            $token = $this->qrVerificationService->generateToken();
            $verificationUrl = $this->qrVerificationService->verificationUrl($token);
            $qrSvg = $this->qrVerificationService->generateSvg($verificationUrl);

            $tanggalSurat = now()->toDateString();
            $pdf = Pdf::loadView('pdf.surat-pengantar', [
                'pengajuan' => $pengajuan,
                'nomorSurat' => $nomorSurat,
                'tanggalSurat' => $tanggalSurat,
                'qrSvg' => $qrSvg,
                'sekolah' => config('simpkl.sekolah'),
            ])->setPaper('a4');

            $fileName = self::DIRECTORY . "/{$pengajuan->kode_pengajuan}.pdf";
            Storage::disk(self::DISK)->put($fileName, $pdf->output());

            SuratPengantar::create([
                'pengajuan_id' => $pengajuan->id,
                'nomor_surat' => $nomorSurat,
                'tanggal_surat' => $tanggalSurat,
                'file_path' => $fileName,
                'verification_token' => $token,
                'generated_at' => now(),
                'generated_by' => $admin->id,
            ]);

            $pengajuan->update(['status' => PengajuanPkl::STATUS_WAITING_COMPANY]);

            AuditLog::record(
                userId: $admin->id,
                action: 'approve_and_generate_letter',
                module: 'pengajuan',
                description: "Admin {$admin->nama_lengkap} memvalidasi pengajuan {$pengajuan->kode_pengajuan}, menetapkan {$guru->nama_lengkap} sebagai pembimbing, dan menerbitkan surat pengantar {$nomorSurat}.",
                targetType: PengajuanPkl::class,
                targetId: $pengajuan->id,
                newValues: ['nomor_surat' => $nomorSurat, 'guru_pembimbing_id' => $guru->id],
            );

            $pengajuan->ketua->notify(new \App\Notifications\PengajuanDisetujuiHubin($pengajuan, $guru, $nomorSurat));

            return $pengajuan->fresh(['perusahaan', 'ketua', 'guruPembimbing', 'suratPengantar']);
        });
    }
}
