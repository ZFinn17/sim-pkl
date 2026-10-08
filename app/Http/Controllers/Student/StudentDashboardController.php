<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $siswaId = $request->user()->id;

        $pengajuanSaya = PengajuanPkl::query()
            ->milikSiswa($siswaId)
            ->with(['perusahaan', 'guruPembimbing'])
            ->latest('tgl_pengajuan')
            ->get();

        $summary = [
            'aktif' => $pengajuanSaya->filter(fn ($p) => $p->isActive())->count(),
            'diterima' => $pengajuanSaya->where('status', PengajuanPkl::STATUS_ACCEPTED)->count(),
            'ditolak' => $pengajuanSaya->where('status', PengajuanPkl::STATUS_REJECTED)->count(),
        ];

        return view('student.dashboard', [
            'pengajuanAktif' => $pengajuanSaya->filter(fn ($p) => $p->isActive())->take(2),
            'summary' => $summary,
        ]);
    }
}
