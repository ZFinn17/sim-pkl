<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratPengantar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLetterController extends Controller
{
    public function index(Request $request): View
    {
        $surat = SuratPengantar::query()
            ->with(['pengajuan.perusahaan', 'pengajuan.ketua', 'generator'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhereHas('pengajuan', fn ($qq) => $qq->where('kode_pengajuan', 'like', "%{$search}%"));
            })
            ->latest('generated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.surat.index', compact('surat'));
    }
}
