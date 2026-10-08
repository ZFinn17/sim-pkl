<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = User::query()
            ->role(User::ROLE_SISWA)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nis_nip', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('kelas'), fn ($query) => $query->where('kelas', $request->string('kelas')))
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->string('status') === 'aktif');
            })
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        $daftarKelas = User::role(User::ROLE_SISWA)
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        return view('admin.siswa.index', compact('siswa', 'daftarKelas'));
    }

    public function create(): View
    {
        return view('admin.siswa.create');
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->toUserAttributes();
        $data['password'] = Hash::make($data['password']);

        $siswa = User::create($data);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'create',
            module: 'siswa',
            description: "Admin menambahkan siswa baru: {$siswa->nama_lengkap} ({$siswa->nis_nip}).",
            targetType: User::class,
            targetId: $siswa->id,
            newValues: $data,
        );

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(User $siswa): View
    {
        abort_unless($siswa->isSiswa(), 404);

        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(UpdateStudentRequest $request, User $siswa): RedirectResponse
    {
        abort_unless($siswa->isSiswa(), 404);

        $old = $siswa->only(['nis_nip', 'nama_lengkap', 'kelas', 'email', 'no_hp']);
        $siswa->update($request->validated());

        AuditLog::record(
            userId: $request->user()->id,
            action: 'update',
            module: 'siswa',
            description: "Admin mengubah data siswa: {$siswa->nama_lengkap}.",
            targetType: User::class,
            targetId: $siswa->id,
            oldValues: $old,
            newValues: $request->validated(),
        );

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function toggleActive(Request $request, User $siswa): RedirectResponse
    {
        abort_unless($siswa->isSiswa(), 404);

        $siswa->is_active = ! $siswa->is_active;
        $siswa->save();

        AuditLog::record(
            userId: $request->user()->id,
            action: $siswa->is_active ? 'activate' : 'deactivate',
            module: 'siswa',
            description: ($siswa->is_active ? 'Admin mengaktifkan' : 'Admin menonaktifkan') . " siswa: {$siswa->nama_lengkap}.",
            targetType: User::class,
            targetId: $siswa->id,
        );

        return back()->with('success', 'Status siswa berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $siswa): RedirectResponse
    {
        abort_unless($siswa->isSiswa(), 404);

        $temporaryPassword = str()->password(10, symbols: false);
        $siswa->update(['password' => Hash::make($temporaryPassword)]);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'reset_password',
            module: 'siswa',
            description: "Admin mereset password siswa: {$siswa->nama_lengkap}.",
            targetType: User::class,
            targetId: $siswa->id,
        );

        return back()->with('success', "Password baru untuk {$siswa->nama_lengkap}: {$temporaryPassword} (catat sekarang, tidak akan ditampilkan lagi).");
    }
}
