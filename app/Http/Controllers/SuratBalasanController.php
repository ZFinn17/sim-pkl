<?php

namespace App\Http\Controllers;

use App\Models\SuratBalasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratBalasanController extends Controller
{
    public function download(Request $request, SuratBalasan $suratBalasan): StreamedResponse
    {
        $pengajuan = $suratBalasan->pengajuan;
        $user = $request->user();

        $authorized = $user->isAdmin()
            || ($user->isSiswa() && (
                $pengajuan->ketua_siswa_id === $user->id
                || $pengajuan->anggotaKelompok()->where('siswa_id', $user->id)->exists()
            ))
            || ($user->isPerusahaan() && $user->perusahaan && $pengajuan->perusahaan_id === $user->perusahaan->id);

        abort_unless($authorized, 403, 'Anda tidak memiliki akses untuk mengunduh file ini.');
        abort_unless(Storage::disk('local')->exists($suratBalasan->file_path), 404, 'File tidak ditemukan.');

        return Storage::disk('local')->download($suratBalasan->file_path, $suratBalasan->original_filename);
    }
}
