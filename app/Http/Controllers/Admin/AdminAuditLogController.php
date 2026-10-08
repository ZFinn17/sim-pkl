<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($request->filled('search'), fn ($q) => $q->where('description', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('module'), fn ($q) => $q->where('module', $request->string('module')))
            ->when($request->filled('dari_tanggal'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('dari_tanggal')))
            ->when($request->filled('sampai_tanggal'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai_tanggal')))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        $modules = AuditLog::query()->distinct()->orderBy('module')->pluck('module');

        return view('admin.audit-log.index', compact('logs', 'modules'));
    }
}
