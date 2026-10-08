<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruPembimbing;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'total_siswa' => User::role(User::ROLE_SISWA)->count(),
            'total_perusahaan' => Perusahaan::count(),
            'perusahaan_aktif' => Perusahaan::aktif()->count(),
            'total_guru' => GuruPembimbing::count(),
            'menunggu_validasi' => PengajuanPkl::status(PengajuanPkl::STATUS_SUBMITTED)->count(),
            'menunggu_perusahaan' => PengajuanPkl::status(PengajuanPkl::STATUS_WAITING_COMPANY)->count(),
            'diterima' => PengajuanPkl::status(PengajuanPkl::STATUS_ACCEPTED)->count(),
            'ditolak' => PengajuanPkl::status(PengajuanPkl::STATUS_REJECTED)->count(),
        ];

        $recentActivity = \App\Models\AuditLog::with('user')
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'summary' => $summary,
            'recentActivity' => $recentActivity,
        ]);
    }
}
