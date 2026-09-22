<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcurementReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $activityQuery = Purchase::with('supplier')
            ->where('created_by', $user->id);

        $purchases = (clone $activityQuery)
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(15);

        $summary = [
            'total' => (clone $activityQuery)->count(),
            'value' => (clone $activityQuery)->sum('total'),
            'pending' => (clone $activityQuery)
                ->where('status', Purchase::STATUS_PENDING_APPROVAL)
                ->count(),
            'received' => (clone $activityQuery)
                ->where('status', Purchase::STATUS_RECEIVED)
                ->count(),
        ];

        return view('reports.procurement', compact('user', 'purchases', 'summary'));
    }
}
