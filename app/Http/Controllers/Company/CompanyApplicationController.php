<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectApplicationRequest;
use App\Models\PengajuanPkl;
use App\Services\QuotaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CompanyApplicationController extends Controller
{
    public function __construct(private readonly QuotaService $quotaService)
    {
    }

    public function index(Request $request): View
    {
        $perusahaan = $request->user()->perusahaan;
        abort_unless($perusahaan, 403, 'Akun Anda belum terhubung dengan profil perusahaan.');

        $pengajuan = PengajuanPkl::query()
            ->where('perusahaan_id', $perusahaan->id)
            ->with(['ketua', 'anggotaKelompok.siswa'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest('tgl_pengajuan')
            ->paginate(15)
            ->withQueryString();

        return view('company.pengajuan.index', compact('pengajuan', 'perusahaan'));
    }

    public function show(Request $request, PengajuanPkl $pengajuan): View
    {
        $this->authorizeCompanyOwnership($request, $pengajuan);

        $pengajuan->load(['ketua', 'anggotaKelompok.siswa', 'guruPembimbing']);

        return view('company.pengajuan.show', compact('pengajuan'));
    }

    public function accept(Request $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        $this->authorizeCompanyOwnership($request, $pengajuan);

        try {
            $this->quotaService->acceptApplication($pengajuan, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pengajuan berhasil diterima. Kuota perusahaan telah diperbarui.');
    }

    public function reject(RejectApplicationRequest $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        $this->authorizeCompanyOwnership($request, $pengajuan);

        try {
            $this->quotaService->rejectApplication($pengajuan, $request->user(), $request->string('alasan_penolakan'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    private function authorizeCompanyOwnership(Request $request, PengajuanPkl $pengajuan): void
    {
        $perusahaan = $request->user()->perusahaan;

        abort_unless($perusahaan && $pengajuan->perusahaan_id === $perusahaan->id, 403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }
}
