<?php

namespace App\Http\Controllers;

use App\Models\CashRemittance;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * ================================================================
     * CEO / ADMIN DASHBOARD
     * ================================================================
     */
    public function admin(Request $request): View
    {
        $today = Carbon::today();

        $monthStart = $today->copy()->startOfMonth();
        $monthEnd   = $today->copy()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        */

        $totalSales = (float) Sale::query()
            ->where('status', 'Completed')
            ->sum('total');

        $monthlySales = (float) Sale::query()
            ->where('status', 'Completed')
            ->whereBetween('sale_date', [
                $monthStart->copy()->startOfDay(),
                $monthEnd->copy()->endOfDay(),
            ])
            ->sum('total');

        $salesCount = Sale::query()
            ->where('status', 'Completed')
            ->count();

        $monthlySalesCount = Sale::query()
            ->where('status', 'Completed')
            ->whereBetween('sale_date', [
                $monthStart->copy()->startOfDay(),
                $monthEnd->copy()->endOfDay(),
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PURCHASES
        |--------------------------------------------------------------------------
        */

        $totalPurchases = (float) Purchase::query()
            ->where('status', '!=', Purchase::STATUS_CANCELLED)
            ->sum('total');

        $monthlyPurchases = (float) Purchase::query()
            ->where('status', '!=', Purchase::STATUS_CANCELLED)
            ->whereBetween('purchase_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->sum('total');

        $purchaseCount = Purchase::query()
            ->where('status', '!=', Purchase::STATUS_CANCELLED)
            ->count();

        $pendingPurchases = Purchase::query()
            ->where('status', Purchase::STATUS_PENDING_APPROVAL)
            ->count();

        $orderedPurchases = Purchase::query()
            ->where('status', Purchase::STATUS_ORDERED)
            ->count();

        $receivedPurchases = Purchase::query()
            ->where('status', Purchase::STATUS_RECEIVED)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | EXPENSES
        |--------------------------------------------------------------------------
        */

        $totalExpenses = (float) Expense::query()
            ->where('status', 'Recorded')
            ->sum('amount');

        $monthlyExpenses = (float) Expense::query()
            ->where('status', 'Recorded')
            ->whereBetween('expense_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->sum('amount');

        $expenseCount = Expense::query()
            ->where('status', 'Recorded')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CASH REMITTANCES
        |--------------------------------------------------------------------------
        */

        $remittanceBase = CashRemittance::query()
            ->where('status', '!=', 'Voided');

        $totalRemittances = (float) (clone $remittanceBase)
            ->sum('actual_amount');

        $remittanceCount = (clone $remittanceBase)->count();

        $monthlyRemittances = (float) (clone $remittanceBase)
            ->whereMonth('remittance_date', $today->month)
            ->whereYear('remittance_date', $today->year)
            ->sum('actual_amount');

        /*
        |--------------------------------------------------------------------------
        | INVENTORY
        |--------------------------------------------------------------------------
        */

        $inventoryItems = InventoryItem::query()
            ->get([
                'id',
                'quantity',
                'minimum_stock',
                'unit_cost',
            ]);

        $inventoryCount = $inventoryItems->count();

        $inventoryValue = (float) $inventoryItems->sum(
            function ($item) {
                return (float) $item->quantity * (float) $item->unit_cost;
            }
        );

        $lowStockCount = $inventoryItems->filter(
            function ($item) {
                return (float) $item->quantity > 0
                    && (float) $item->quantity <= (float) $item->minimum_stock;
            }
        )->count();

        $outOfStockCount = $inventoryItems->filter(
            function ($item) {
                return (float) $item->quantity <= 0;
            }
        )->count();

        $normalStockCount = max(
            0,
            $inventoryCount - $lowStockCount - $outOfStockCount
        );

        /*
        |--------------------------------------------------------------------------
        | FINANCIAL POSITION
        |--------------------------------------------------------------------------
        */

        $netPosition =
            $totalSales
            - $totalPurchases
            - $totalExpenses;

        $monthlyNetPosition =
            $monthlySales
            - $monthlyPurchases
            - $monthlyExpenses;

        /*
        |--------------------------------------------------------------------------
        | 30-DAY SALES VS PURCHASES GRAPH
        |--------------------------------------------------------------------------
        */

        $trendStart = $today->copy()->subDays(29);

        $salesByDay = Sale::query()
            ->where('status', 'Completed')
            ->whereBetween('sale_date', [
                $trendStart->copy()->startOfDay(),
                $today->copy()->endOfDay(),
            ])
            ->selectRaw('DATE(sale_date) as report_date, SUM(total) as total')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->pluck('total', 'report_date');

        $purchasesByDay = Purchase::query()
            ->where('status', '!=', Purchase::STATUS_CANCELLED)
            ->whereBetween('purchase_date', [
                $trendStart->toDateString(),
                $today->toDateString(),
            ])
            ->selectRaw('purchase_date as report_date, SUM(total) as total')
            ->groupBy('purchase_date')
            ->pluck('total', 'report_date');

        $salesPurchaseTrend = [];

        for (
            $date = $trendStart->copy();
            $date->lte($today);
            $date->addDay()
        ) {
            $key = $date->toDateString();

            $salesPurchaseTrend[] = [
                'date'      => $date->format('M d'),
                'sales'     => round((float) ($salesByDay[$key] ?? 0), 2),
                'purchases' => round((float) ($purchasesByDay[$key] ?? 0), 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | EXPENSE BREAKDOWN GRAPH
        |--------------------------------------------------------------------------
        */

        $expenseBreakdown = Expense::query()
            ->where('status', 'Recorded')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($expense) {
                return [
                    'category' => $expense->category,
                    'total'    => round((float) $expense->total, 2),
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | PURCHASE STATUS GRAPH
        |--------------------------------------------------------------------------
        */

        $purchaseStatusOrder = [
            Purchase::STATUS_DRAFT,
            Purchase::STATUS_PENDING_APPROVAL,
            Purchase::STATUS_APPROVED,
            Purchase::STATUS_REJECTED,
            Purchase::STATUS_ORDERED,
            Purchase::STATUS_PARTIALLY_RECEIVED,
            Purchase::STATUS_RECEIVED,
            Purchase::STATUS_CANCELLED,
        ];

        $purchaseStatusCounts = Purchase::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $purchaseStatuses = [];

        foreach ($purchaseStatusOrder as $status) {
            $purchaseStatuses[] = [
                'status' => $status,
                'count'  => (int) ($purchaseStatusCounts[$status] ?? 0),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | INVENTORY HEALTH GRAPH
        |--------------------------------------------------------------------------
        */

        $inventoryHealth = [
            ['status' => 'Normal',     'count' => $normalStockCount],
            ['status' => 'Low Stock',  'count' => $lowStockCount],
            ['status' => 'Out of Stock', 'count' => $outOfStockCount],
        ];

        /*
        |--------------------------------------------------------------------------
        | RECENT ACTIVITY
        |--------------------------------------------------------------------------
        */

        $recentSales = Sale::query()
            ->with('creator')
            ->where('status', 'Completed')
            ->latest('sale_date')
            ->limit(6)
            ->get();

        $recentPurchases = Purchase::query()
            ->with(['supplier', 'creator'])
            ->where('status', '!=', Purchase::STATUS_CANCELLED)
            ->latest('purchase_date')
            ->limit(6)
            ->get();

        $recentExpenses = Expense::query()
            ->with('user')
            ->where('status', 'Recorded')
            ->latest('expense_date')
            ->limit(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('dashboard.admin', [

            'user' => $request->user(),

            // Sales
            'totalSales'        => $totalSales,
            'monthlySales'      => $monthlySales,
            'salesCount'        => $salesCount,
            'monthlySalesCount' => $monthlySalesCount,

            // Purchases
            'totalPurchases'    => $totalPurchases,
            'monthlyPurchases'  => $monthlyPurchases,
            'purchaseCount'     => $purchaseCount,
            'pendingPurchases'  => $pendingPurchases,
            'orderedPurchases'  => $orderedPurchases,
            'receivedPurchases' => $receivedPurchases,

            // Expenses
            'totalExpenses'   => $totalExpenses,
            'monthlyExpenses' => $monthlyExpenses,
            'expenseCount'    => $expenseCount,

            // Cash Remittances
            'totalRemittances'   => $totalRemittances,
            'remittanceCount'    => $remittanceCount,
            'monthlyRemittances' => $monthlyRemittances,

            // Inventory
            'inventoryCount'    => $inventoryCount,
            'inventoryValue'    => $inventoryValue,
            'normalStockCount'  => $normalStockCount,
            'lowStockCount'     => $lowStockCount,
            'outOfStockCount'   => $outOfStockCount,

            // Financial position
            'netPosition'        => $netPosition,
            'monthlyNetPosition' => $monthlyNetPosition,

            // Graphs
            'salesPurchaseTrend' => $salesPurchaseTrend,
            'expenseBreakdown'   => $expenseBreakdown,
            'purchaseStatuses'   => $purchaseStatuses,
            'inventoryHealth'    => $inventoryHealth,

            // Recent activity
            'recentSales'     => $recentSales,
            'recentPurchases' => $recentPurchases,
            'recentExpenses'  => $recentExpenses,
        ]);
    }


    /**
     * ================================================================
     * FINANCE DASHBOARD
     * ================================================================
     */
    public function finance(Request $request): View
    {
        return view('dashboard.finance', [
            'user' => $request->user(),
        ]);
    }

}