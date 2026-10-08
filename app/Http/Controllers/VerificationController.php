<?php

namespace App\Http\Controllers;

use App\Models\SuratPengantar;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Halaman publik verifikasi surat pengantar via QR.
     * Tidak boleh membocorkan data sensitif (email/no HP pribadi, dsb).
     */
    public function show(string $token): View
    {
        $surat = SuratPengantar::query()
            ->where('verification_token', $token)
            ->with(['pengajuan.perusahaan'])
            ->first();

        if (! $surat) {
            return view('verification.invalid');
        }

        return view('verification.show', ['surat' => $surat, 'pengajuan' => $surat->pengajuan]);
    }
}
