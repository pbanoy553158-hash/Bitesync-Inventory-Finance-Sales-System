<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * CEO/Admin dashboard.
     */
    public function admin(Request $request): View
    {
        return view('dashboard.admin', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Finance dashboard.
     */
    public function finance(Request $request): View
    {
        return view('dashboard.finance', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Procurement dashboard.
     */
    public function procurement(Request $request): View
    {
        $user = $request->user();

        return view('dashboard.procurement', [
            'user' => $user,
            'recentPurchases' => Purchase::with('supplier')
                ->where('created_by', $user->id)
                ->latest('purchase_date')
                ->latest('id')
                ->limit(5)
                ->get(),
            'stats' => [
                'total' => Purchase::where('created_by', $user->id)->count(),
                'draft' => Purchase::where('created_by', $user->id)
                    ->where('status', Purchase::STATUS_DRAFT)->count(),
                'pending' => Purchase::where('created_by', $user->id)
                    ->where('status', Purchase::STATUS_PENDING_APPROVAL)->count(),
                'received' => Purchase::where('created_by', $user->id)
                    ->where('status', Purchase::STATUS_RECEIVED)->count(),
            ],
        ]);
    }
}