<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreApplicationRequest;
use App\Http\Requests\Student\UploadSuratBalasanRequest;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\SuratBalasan;
use App\Models\User;
use App\Notifications\SuratBalasanDiupload;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applicationService)
    {
    }

    public function index(Request $request): View
    {
        $pengajuan = PengajuanPkl::query()
            ->milikSiswa($request->user()->id)
            ->with(['perusahaan', 'guruPembimbing', 'anggotaKelompok.siswa'])
            ->latest('tgl_pengajuan')
            ->paginate(10);

        return view('student.pengajuan.index', compact('pengajuan'));
    }

    public function create(Request $request): View
    {
        $perusahaanTerpilih = null;
        if ($request->filled('perusahaan')) {
            $perusahaanTerpilih = Perusahaan::aktif()->find($request->integer('perusahaan'));
        }

        $calonAnggota = User::query()
            ->role(User::ROLE_SISWA)
            ->active()
            ->where('id', '!=', $request->user()->id)
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nis_nip', 'kelas']);

        $perusahaanAktif = Perusahaan::aktif()->orderBy('nama_perusahaan')->get();

        return view('student.pengajuan.create', compact('perusahaanTerpilih', 'calonAnggota', 'perusahaanAktif'));
    }

    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        $perusahaan = Perusahaan::findOrFail($request->integer('perusahaan_id'));

        try {
            $pengajuan = $this->applicationService->createApplication(
                ketua: $request->user(),
                perusahaan: $perusahaan,
                jenisPengajuan: $request->string('jenis_pengajuan'),
                tglMulai: $request->string('tgl_mulai_pkl'),
                tglSelesai: $request->string('tgl_selesai_pkl'),
                anggotaIds: $request->input('anggota', []),
            );
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()
            ->route('student.pengajuan.show', $pengajuan)
            ->with('success', "Pengajuan PKL berhasil dikirim dengan kode {$pengajuan->kode_pengajuan}. Menunggu validasi Admin Hubin.");
    }

    public function show(Request $request, PengajuanPkl $pengajuan): View
    {
        $this->authorizeOwnership($request->user(), $pengajuan);

        $pengajuan->load(['perusahaan', 'guruPembimbing', 'anggotaKelompok.siswa', 'suratPengantar', 'suratBalasan']);

        return view('student.pengajuan.show', compact('pengajuan'));
    }

    public function cancel(Request $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        $this->authorizeOwnership($request->user(), $pengajuan);

        try {
            $this->applicationService->cancelByStudent($pengajuan, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    /**
     * Upload surat balasan perusahaan. Hanya boleh untuk pengajuan yang sudah accepted
     * (lihat spec bagian 50: siswa upload surat balasan setelah perusahaan menerima).
     */
    public function uploadSuratBalasan(UploadSuratBalasanRequest $request, PengajuanPkl $pengajuan): RedirectResponse
    {
        $this->authorizeOwnership($request->user(), $pengajuan);

        if ($pengajuan->status !== PengajuanPkl::STATUS_ACCEPTED) {
            return back()->withErrors(['file' => 'Surat balasan hanya dapat diupload untuk pengajuan yang sudah diterima perusahaan.']);
        }

        $file = $request->file('file');
        $path = $file->store('surat-balasan/' . $pengajuan->id, 'local');

        $suratBalasan = SuratBalasan::create([
            'pengajuan_id' => $pengajuan->id,
            'uploaded_by' => $request->user()->id,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'uploaded_at' => now(),
            'catatan' => $request->input('catatan'),
        ]);

        \App\Models\AuditLog::record(
            userId: $request->user()->id,
            action: 'upload_surat_balasan',
            module: 'pengajuan',
            description: "Siswa {$request->user()->nama_lengkap} mengupload surat balasan untuk {$pengajuan->kode_pengajuan}.",
            targetType: PengajuanPkl::class,
            targetId: $pengajuan->id,
        );

        $request->user()->notify(new SuratBalasanDiupload($pengajuan));

        unset($suratBalasan);

        return back()->with('success', 'Surat balasan berhasil diupload.');
    }

    private function authorizeOwnership(User $siswa, PengajuanPkl $pengajuan): void
    {
        $isKetua = $pengajuan->ketua_siswa_id === $siswa->id;
        $isAnggota = $pengajuan->anggotaKelompok()->where('siswa_id', $siswa->id)->exists();

        abort_unless($isKetua || $isAnggota, 403, 'Anda tidak memiliki akses untuk melihat pengajuan ini.');
    }
}
