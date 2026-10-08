<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\AuditLog;
use App\Models\GuruPembimbing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTeacherController extends Controller
{
    public function index(Request $request): View
    {
        $guru = GuruPembimbing::query()
            ->when($request->filled('search'), fn ($query) => $query->where('nama_lengkap', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        return view('admin.guru.index', compact('guru'));
    }

    public function create(): View
    {
        return view('admin.guru.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $guru = GuruPembimbing::create($request->validated());

        AuditLog::record(
            userId: $request->user()->id,
            action: 'create',
            module: 'guru_pembimbing',
            description: "Admin menambahkan guru pembimbing: {$guru->nama_lengkap}.",
            targetType: GuruPembimbing::class,
            targetId: $guru->id,
        );

        return redirect()->route('admin.guru.index')->with('success', 'Guru pembimbing berhasil ditambahkan.');
    }

    public function edit(GuruPembimbing $guru): View
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(UpdateTeacherRequest $request, GuruPembimbing $guru): RedirectResponse
    {
        $old = $guru->only(['nama_lengkap', 'nip', 'status']);
        $guru->update($request->validated());

        AuditLog::record(
            userId: $request->user()->id,
            action: 'update',
            module: 'guru_pembimbing',
            description: "Admin mengubah data guru pembimbing: {$guru->nama_lengkap}.",
            targetType: GuruPembimbing::class,
            targetId: $guru->id,
            oldValues: $old,
            newValues: $request->validated(),
        );

        return redirect()->route('admin.guru.index')->with('success', 'Data guru pembimbing berhasil diperbarui.');
    }

    public function toggleActive(Request $request, GuruPembimbing $guru): RedirectResponse
    {
        $guru->status = $guru->isAktif() ? 'nonaktif' : 'aktif';
        $guru->save();

        AuditLog::record(
            userId: $request->user()->id,
            action: $guru->isAktif() ? 'activate' : 'deactivate',
            module: 'guru_pembimbing',
            description: ($guru->isAktif() ? 'Admin mengaktifkan' : 'Admin menonaktifkan') . " guru pembimbing: {$guru->nama_lengkap}.",
            targetType: GuruPembimbing::class,
            targetId: $guru->id,
        );

        return back()->with('success', 'Status guru pembimbing berhasil diperbarui.');
    }
}
