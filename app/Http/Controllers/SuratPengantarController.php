<?php

namespace App\Http\Controllers;

use App\Models\SuratPengantar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratPengantarController extends Controller
{
    public function download(Request $request, SuratPengantar $surat): StreamedResponse
    {
        $pengajuan = $surat->pengajuan;
        $user = $request->user();

        $authorized = $user->isAdmin()
            || ($user->isSiswa() && (
                $pengajuan->ketua_siswa_id === $user->id
                || $pengajuan->anggotaKelompok()->where('siswa_id', $user->id)->exists()
            ))
            || ($user->isPerusahaan() && $user->perusahaan && $pengajuan->perusahaan_id === $user->perusahaan->id);

        abort_unless($authorized, 403, 'Anda tidak memiliki akses untuk mengunduh surat ini.');
        abort_unless(Storage::disk('local')->exists($surat->file_path), 404, 'File surat tidak ditemukan.');

        return Storage::disk('local')->download(
            $surat->file_path,
            "Surat-Pengantar-PKL-{$pengajuan->kode_pengajuan}.pdf",
        );
    }
}
