<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $auditLogs = AuditLog::query()
            ->with('user:id,name,email')
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('action', 'like', "%{$search}%")
                        ->orWhere('route_name', 'like', "%{$search}%")
                        ->orWhere('subject_type', 'like', "%{$search}%")
                        ->orWhere('subject_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($validated['from'] ?? null, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($validated['to'] ?? null, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.audit-logs', [
            'auditLogs' => $auditLogs,
            'filters' => $validated,
        ]);
    }
}