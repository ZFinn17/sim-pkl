<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectApplicationRequest;
use App\Models\AuditLog;
use App\Models\GuruPembimbing;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Services\PdfLetterService;
use App\Services\QuotaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminApplicationController extends Controller
{
    public function __construct(
        private readonly PdfLetterService $pdfLetterService,
        private readonly QuotaService $quotaService,
    ) {
    }

    public function index(Request $request): View
    {
        $pengajuan = PengajuanPkl::query()
            ->with(['ketua', 'perusahaan', 'guruPembimbing'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                    ->orWhereHas('ketua', fn ($qq) => $qq->where('nama_lengkap', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('perusahaan_id'), fn ($q) => $q->where('perusahaan_id', $request->integer('perusahaan_id')))
            ->when($request->filled('dari_tanggal'), fn ($q) => $q->whereDate('tgl_pengajuan', '>=', $request->date('dari_tanggal')))
            ->when($request->filled('sampai_tanggal'), fn ($q) => $q->whereDate('tgl_pengajuan', '<=', $request->date('sampai_tanggal')))
            ->latest('tgl_pengajuan')
            ->paginate(15)
            ->withQueryString();

        $perusahaanList = Perusahaan::orderBy('nama_perusahaan')->get(['id', 'nama_perusahaan']);

        return view('admin.pengajuan.index', compact('pengajuan', 'perusahaanList'));
    }

    public function show(PengajuanPkl $pengajuan): View
    {
        $pengajuan->load(['ketua', 'perusahaan', 'guruPembimbing', 'anggotaKelompok.siswa', 'approvedBy', 'suratPengantar', 'suratBalasan.uploader']);

        $guruAktif = GuruPembimbing::aktif()->orderBy('nama_lengkap')->get();

        return view('admin.pengajuan.show', compact('pengajuan', 'guruAktif'));
    }

    /**
     * Satu aksi atomik: validasi Hubin + assign guru pembimbing + generate
     * nomor surat, QR, dan PDF surat pengantar, lalu lanjut ke status
     * "Menunggu Perusahaan". Lihat PdfLetterService untuk detail transaksinya.
     */
    public function approve(Request $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'guru_pembimbing_id' => ['required', 'integer', 'exists:guru_pembimbing,id'],
        ], [], ['guru_pembimbing_id' => 'guru pembimbing']);

        $guru = GuruPembimbing::findOrFail($validated['guru_pembimbing_id']);

        try {
            $this->pdfLetterService->approveAndGenerate($pengajuan, $guru, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pengajuan disetujui. Guru pembimbing ditetapkan dan surat pengantar berhasil diterbitkan.');
    }

    public function reject(RejectApplicationRequest $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        if (! in_array($pengajuan->status, [PengajuanPkl::STATUS_SUBMITTED, PengajuanPkl::STATUS_HUBIN_APPROVED], true)) {
            return back()->withErrors(['pengajuan' => 'Pengajuan ini tidak dapat ditolak pada status saat ini.']);
        }

        $pengajuan->update([
            'status' => PengajuanPkl::STATUS_REJECTED,
            'alasan_penolakan' => $request->string('alasan_penolakan'),
            'rejected_at' => now(),
        ]);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'reject',
            module: 'pengajuan',
            description: "Admin {$request->user()->nama_lengkap} menolak pengajuan {$pengajuan->kode_pengajuan}.",
            targetType: PengajuanPkl::class,
            targetId: $pengajuan->id,
        );

        $pengajuan->ketua->notify(new \App\Notifications\PengajuanDitolak($pengajuan, \App\Notifications\PengajuanDitolak::OLEH_HUBIN));

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    /**
     * Buka kembali kesempatan siswa mengajukan ulang ke perusahaan yang
     * sebelumnya menolak pengajuannya (business rule: reapply perlu kontrol Admin).
     */
    public function reopen(Request $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        if ($pengajuan->status !== PengajuanPkl::STATUS_REJECTED) {
            return back()->withErrors(['pengajuan' => 'Hanya pengajuan yang ditolak yang dapat dibuka kembali.']);
        }

        $pengajuan->update(['reopened_at' => now()]);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'reopen',
            module: 'pengajuan',
            description: "Admin {$request->user()->nama_lengkap} membuka kembali kesempatan mengajukan ulang ke {$pengajuan->perusahaan->nama_perusahaan} untuk {$pengajuan->ketua->nama_lengkap}.",
            targetType: PengajuanPkl::class,
            targetId: $pengajuan->id,
        );

        return back()->with('success', 'Kesempatan mengajukan ulang ke perusahaan ini telah dibuka untuk siswa tersebut.');
    }

    /**
     * Pembatalan pengajuan yang SUDAH diterima perusahaan. Siswa tidak boleh
     * membatalkan sendiri pada status ini (lihat PengajuanPkl::canBeCancelledByStudent) —
     * hanya Admin yang bisa, dan kuota yang sebelumnya terpakai dikembalikan.
     */
    public function cancelAccepted(Request $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        try {
            $this->quotaService->cancelAcceptedByAdmin($pengajuan, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pengajuan yang sudah diterima berhasil dibatalkan. Kuota perusahaan telah dikembalikan.');
    }
}
