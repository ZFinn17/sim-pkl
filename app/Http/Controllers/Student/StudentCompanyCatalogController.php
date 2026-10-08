<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentCompanyCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $perusahaan = Perusahaan::query()
            ->aktif()
            ->when($request->filled('search'), fn ($q) => $q->where('nama_perusahaan', 'like', '%' . $request->string('search') . '%'))
            ->when($request->boolean('hanya_tersedia'), fn ($q) => $q->whereColumn('kuota_terisi', '<', 'kuota_maksimal'))
            ->orderBy('nama_perusahaan')
            ->paginate(9)
            ->withQueryString();

        return view('student.perusahaan.index', compact('perusahaan'));
    }

    public function show(Perusahaan $perusahaan): View
    {
        abort_unless($perusahaan->isAktif(), 404);

        return view('student.perusahaan.show', compact('perusahaan'));
    }
}
