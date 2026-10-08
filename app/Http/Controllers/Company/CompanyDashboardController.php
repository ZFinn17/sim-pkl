<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $perusahaan = $request->user()->perusahaan;

        $pengajuan = PengajuanPkl::where('perusahaan_id', $perusahaan?->id)->get();

        $summary = [
            'masuk' => $pengajuan->count(),
            'menunggu' => $pengajuan->where('status', PengajuanPkl::STATUS_WAITING_COMPANY)->count(),
            'diterima' => $pengajuan->where('status', PengajuanPkl::STATUS_ACCEPTED)->count(),
            'ditolak' => $pengajuan->where('status', PengajuanPkl::STATUS_REJECTED)->count(),
        ];

        return view('company.dashboard', [
            'perusahaan' => $perusahaan,
            'summary' => $summary,
            'recentApplications' => $pengajuan->sortByDesc('tgl_pengajuan')->take(5),
        ]);
    }
}
