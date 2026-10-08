<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Models\AuditLog;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminCompanyController extends Controller
{
    public function index(Request $request): View
    {
        $perusahaan = Perusahaan::query()
            ->when($request->filled('search'), fn ($query) => $query->where('nama_perusahaan', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status_mitra', $request->string('status')))
            ->orderBy('nama_perusahaan')
            ->paginate(15)
            ->withQueryString();

        return view('admin.perusahaan.index', compact('perusahaan'));
    }

    public function create(): View
    {
        return view('admin.perusahaan.create');
    }

    /**
     * Buat perusahaan sekaligus akun login-nya dalam satu DB transaction.
     * Password sementara dibuat sistem dan ditampilkan SEKALI ke admin.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $temporaryPassword = str()->password(10, symbols: false);

        $perusahaan = DB::transaction(function () use ($request, $temporaryPassword) {
            $user = User::create([
                'nis_nip' => null,
                'nama_lengkap' => $request->string('nama_pic'),
                'kelas' => null,
                'role' => User::ROLE_PERUSAHAAN,
                'no_hp' => $request->string('kontak_hrd'),
                'is_active' => true,
                'email' => $request->string('email_login'),
                'password' => Hash::make($temporaryPassword),
            ]);

            return Perusahaan::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $request->string('nama_perusahaan'),
                'alamat_lengkap' => $request->string('alamat_lengkap'),
                'nama_pic' => $request->string('nama_pic'),
                'kontak_hrd' => $request->string('kontak_hrd'),
                'email_hrd' => $request->string('email_hrd'),
                'kuota_maksimal' => $request->integer('kuota_maksimal'),
                'kuota_terisi' => 0,
                'status_mitra' => $request->string('status_mitra'),
                'deskripsi' => $request->string('deskripsi'),
            ]);
        });

        AuditLog::record(
            userId: $request->user()->id,
            action: 'create',
            module: 'perusahaan',
            description: "Admin menambahkan perusahaan baru: {$perusahaan->nama_perusahaan}.",
            targetType: Perusahaan::class,
            targetId: $perusahaan->id,
        );

        return redirect()->route('admin.perusahaan.index')->with(
            'success',
            "Perusahaan berhasil ditambahkan. Email login: {$request->string('email_login')} | Password sementara: {$temporaryPassword} (catat sekarang)."
        );
    }

    public function edit(Perusahaan $perusahaan): View
    {
        return view('admin.perusahaan.edit', compact('perusahaan'));
    }

    public function update(UpdateCompanyRequest $request, Perusahaan $perusahaan): RedirectResponse
    {
        $old = $perusahaan->only(['nama_perusahaan', 'kuota_maksimal', 'status_mitra']);

        $perusahaan->update($request->validated());

        if ($old['kuota_maksimal'] !== $perusahaan->kuota_maksimal) {
            AuditLog::record(
                userId: $request->user()->id,
                action: 'update_kuota',
                module: 'perusahaan',
                description: "Admin mengubah kuota {$perusahaan->nama_perusahaan} dari {$old['kuota_maksimal']} menjadi {$perusahaan->kuota_maksimal}.",
                targetType: Perusahaan::class,
                targetId: $perusahaan->id,
                oldValues: ['kuota_maksimal' => $old['kuota_maksimal']],
                newValues: ['kuota_maksimal' => $perusahaan->kuota_maksimal],
            );
        }

        AuditLog::record(
            userId: $request->user()->id,
            action: 'update',
            module: 'perusahaan',
            description: "Admin mengubah data perusahaan: {$perusahaan->nama_perusahaan}.",
            targetType: Perusahaan::class,
            targetId: $perusahaan->id,
            oldValues: $old,
            newValues: $request->validated(),
        );

        return redirect()->route('admin.perusahaan.index')->with('success', 'Data perusahaan berhasil diperbarui.');
    }

    public function toggleActive(Request $request, Perusahaan $perusahaan): RedirectResponse
    {
        $perusahaan->status_mitra = $perusahaan->isAktif() ? 'nonaktif' : 'aktif';
        $perusahaan->save();

        AuditLog::record(
            userId: $request->user()->id,
            action: $perusahaan->isAktif() ? 'activate' : 'deactivate',
            module: 'perusahaan',
            description: ($perusahaan->isAktif() ? 'Admin mengaktifkan' : 'Admin menonaktifkan') . " perusahaan: {$perusahaan->nama_perusahaan}.",
            targetType: Perusahaan::class,
            targetId: $perusahaan->id,
        );

        return back()->with('success', 'Status perusahaan berhasil diperbarui.');
    }
}
