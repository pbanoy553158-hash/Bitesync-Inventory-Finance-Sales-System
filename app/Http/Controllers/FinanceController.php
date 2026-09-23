<?php

namespace App\Http\Controllers;

use App\Models\CashRemittance;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    /**
     * Display the Finance Dashboard.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized.');
        }


        /*
        |--------------------------------------------------------------------------
        | REPORTING PERIOD
        |--------------------------------------------------------------------------
        |
        | If the Finance user selects a date range, use that range.
        |
        | If no date range is selected, show ALL available transaction
        | records instead of restricting the dashboard to the current month.
        |
        | This allows Finance to see sales already created by Admin,
        | Staff, or other authorized users.
        |
        */

        $hasStartDate = $request->filled('start_date');
        $hasEndDate = $request->filled('end_date');


        try {

            $startDate = $hasStartDate
                ? Carbon::parse(
                    $request->input('start_date')
                )->startOfDay()
                : Carbon::create(
                    2000,
                    1,
                    1,
                    0,
                    0,
                    0
                );

        } catch (\Throwable $e) {

            $startDate = Carbon::create(
                2000,
                1,
                1,
                0,
                0,
                0
            );

        }


        try {

            $endDate = $hasEndDate
                ? Carbon::parse(
                    $request->input('end_date')
                )->endOfDay()
                : now()->endOfDay();

        } catch (\Throwable $e) {

            $endDate = now()->endOfDay();

        }


        /*
        |--------------------------------------------------------------------------
        | CORRECT REVERSED DATE RANGE
        |--------------------------------------------------------------------------
        */

        if ($startDate->greaterThan($endDate)) {

            [$startDate, $endDate] = [

                $endDate
                    ->copy()
                    ->startOfDay(),

                $startDate
                    ->copy()
                    ->endOfDay(),

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | DATE VALUES
        |--------------------------------------------------------------------------
        */

        $dateStart = $startDate->toDateString();

        $dateEnd = $endDate->toDateString();


        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Finance does NOT filter sales by the Finance user's ID.
        |
        | Finance should see company-wide completed sales that already
        | exist in the Sales module.
        |
        */

        $salesQuery = Sale::query()
            ->where('status', 'Completed')
            ->whereBetween(
                'sale_date',
                [
                    $startDate->copy()->startOfDay(),
                    $endDate->copy()->endOfDay(),
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | TOTAL SALES
        |--------------------------------------------------------------------------
        */

        $totalSales = (float) $salesQuery->sum('total');


        /*
        |--------------------------------------------------------------------------
        | SALES COUNT
        |--------------------------------------------------------------------------
        */

        $salesCount =
            (clone $salesQuery)->count();


        /*
        |--------------------------------------------------------------------------
        | AMOUNT RECEIVED
        |--------------------------------------------------------------------------
        */

        $totalAmountReceived =
            (float) (clone $salesQuery)
                ->sum('amount_received');


        /*
        |--------------------------------------------------------------------------
        | CHANGE GIVEN
        |--------------------------------------------------------------------------
        */

        $totalChange =
            (float) (clone $salesQuery)
                ->sum('change');


        /*
        |--------------------------------------------------------------------------
        | RECENT SALES
        |--------------------------------------------------------------------------
        */

        $recentSales =
            (clone $salesQuery)
                ->orderByDesc('sale_date')
                ->orderByDesc('id')
                ->limit(8)
                ->get();


        /*
        |--------------------------------------------------------------------------
        | SALES ACTIVITY
        |--------------------------------------------------------------------------
        */

        $salesActivity =
            (clone $salesQuery)
                ->selectRaw(
                    'DATE(sale_date) as date'
                )
                ->selectRaw(
                    'SUM(total) as total'
                )
                ->groupByRaw(
                    'DATE(sale_date)'
                )
                ->orderBy('date')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | PAYMENT METHOD SUMMARY
        |--------------------------------------------------------------------------
        */

        $paymentMethodSummary =
            (clone $salesQuery)
                ->selectRaw(
                    "COALESCE(payment_method, 'Unspecified') as payment_method"
                )
                ->selectRaw(
                    'COUNT(*) as transaction_count'
                )
                ->selectRaw(
                    'SUM(total) as total_amount'
                )
                ->groupByRaw(
                    "COALESCE(payment_method, 'Unspecified')"
                )
                ->orderByDesc('total_amount')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | PURCHASES
        |--------------------------------------------------------------------------
        |
        | Draft, Rejected, and Cancelled purchases are excluded from
        | actual purchasing totals.
        |
        */

        $purchaseQuery =
            Purchase::query()
                ->whereBetween(
                    'purchase_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->whereNotIn(
                    'status',
                    [
                        Purchase::STATUS_DRAFT,
                        Purchase::STATUS_REJECTED,
                        Purchase::STATUS_CANCELLED,
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | TOTAL PURCHASES
        |--------------------------------------------------------------------------
        */

        $totalPurchases =
            (float) $purchaseQuery->sum('total');


        /*
        |--------------------------------------------------------------------------
        | PURCHASE COUNT
        |--------------------------------------------------------------------------
        */

        $purchaseCount =
            (clone $purchaseQuery)->count();


        /*
        |--------------------------------------------------------------------------
        | PURCHASE STATUS SUMMARY
        |--------------------------------------------------------------------------
        */

        $purchaseStatusSummary =
            Purchase::query()
                ->whereBetween(
                    'purchase_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->select('status')
                ->selectRaw(
                    'COUNT(*) as transaction_count'
                )
                ->selectRaw(
                    'SUM(total) as total_amount'
                )
                ->groupBy('status')
                ->orderByDesc('transaction_count')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | RECENT PURCHASES
        |--------------------------------------------------------------------------
        */

        $recentPurchases =
            Purchase::with([
                'supplier',
                'creator',
            ])
                ->whereBetween(
                    'purchase_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->whereNotIn(
                    'status',
                    [
                        Purchase::STATUS_DRAFT,
                        Purchase::STATUS_REJECTED,
                        Purchase::STATUS_CANCELLED,
                    ]
                )
                ->orderByDesc('purchase_date')
                ->orderByDesc('id')
                ->limit(8)
                ->get();


        /*
        |--------------------------------------------------------------------------
        | EXPENSES
        |--------------------------------------------------------------------------
        */

        $expenseQuery =
            Expense::query()
                ->whereBetween(
                    'expense_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->where(function ($query) {

                    $query
                        ->whereNull('status')
                        ->orWhere(
                            'status',
                            '!=',
                            'Cancelled'
                        );

                });


        /*
        |--------------------------------------------------------------------------
        | TOTAL EXPENSES
        |--------------------------------------------------------------------------
        */

        $totalExpenses =
            (float) $expenseQuery->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | EXPENSE COUNT
        |--------------------------------------------------------------------------
        */

        $expenseCount =
            (clone $expenseQuery)->count();


        /*
        |--------------------------------------------------------------------------
        | EXPENSE CATEGORY SUMMARY
        |--------------------------------------------------------------------------
        */

        $expenseCategorySummary =
            Expense::query()
                ->whereBetween(
                    'expense_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->where(function ($query) {

                    $query
                        ->whereNull('status')
                        ->orWhere(
                            'status',
                            '!=',
                            'Cancelled'
                        );

                })
                ->selectRaw(
                    "COALESCE(category, 'Uncategorized') as category"
                )
                ->selectRaw(
                    'COUNT(*) as transaction_count'
                )
                ->selectRaw(
                    'SUM(amount) as total_amount'
                )
                ->groupByRaw(
                    "COALESCE(category, 'Uncategorized')"
                )
                ->orderByDesc('total_amount')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | RECENT EXPENSES
        |--------------------------------------------------------------------------
        */

        $recentExpenses =
            Expense::with('user')
                ->whereBetween(
                    'expense_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->where(function ($query) {

                    $query
                        ->whereNull('status')
                        ->orWhere(
                            'status',
                            '!=',
                            'Cancelled'
                        );

                })
                ->orderByDesc('expense_date')
                ->orderByDesc('id')
                ->limit(8)
                ->get();


        /*
        |--------------------------------------------------------------------------
        | CASH REMITTANCE
        |--------------------------------------------------------------------------
        */

        $remittanceQuery =
            CashRemittance::query()
                ->whereBetween(
                    'remittance_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | EXPECTED REMITTANCE
        |--------------------------------------------------------------------------
        */

        $expectedRemittance =
            (float) (clone $remittanceQuery)
                ->sum('expected_amount');


        /*
        |--------------------------------------------------------------------------
        | ACTUAL REMITTANCE
        |--------------------------------------------------------------------------
        */

        $actualRemittance =
            (float) (clone $remittanceQuery)
                ->sum('actual_amount');


        /*
        |--------------------------------------------------------------------------
        | REMITTANCE VARIANCE
        |--------------------------------------------------------------------------
        */

        $remittanceVariance =
            $actualRemittance -
            $expectedRemittance;


        /*
        |--------------------------------------------------------------------------
        | REMITTANCE COUNT
        |--------------------------------------------------------------------------
        */

        $remittanceCount =
            (clone $remittanceQuery)->count();


        /*
        |--------------------------------------------------------------------------
        | RECENT CASH REMITTANCES
        |--------------------------------------------------------------------------
        */

        $recentRemittances =
            CashRemittance::with('user')
                ->whereBetween(
                    'remittance_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->orderByDesc('remittance_date')
                ->orderByDesc('id')
                ->limit(8)
                ->get();


        /*
        |--------------------------------------------------------------------------
        | NET AMOUNT
        |--------------------------------------------------------------------------
        |
        | Net Amount =
        |
        | Total Sales
        | - Total Purchases
        | - Total Expenses
        |
        */

        $netAmount =
            $totalSales
            - $totalPurchases
            - $totalExpenses;


        /*
        |--------------------------------------------------------------------------
        | CANCELLED SALES
        |--------------------------------------------------------------------------
        */

        $cancelledSalesCount =
            Sale::query()
                ->where('status', 'Cancelled')
                ->whereBetween(
                    'sale_date',
                    [
                        $startDate->copy()->startOfDay(),
                        $endDate->copy()->endOfDay(),
                    ]
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | CANCELLED PURCHASES
        |--------------------------------------------------------------------------
        */

        $cancelledPurchasesCount =
            Purchase::query()
                ->where(
                    'status',
                    Purchase::STATUS_CANCELLED
                )
                ->whereBetween(
                    'purchase_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | CANCELLED EXPENSES
        |--------------------------------------------------------------------------
        */

        $cancelledExpensesCount =
            Expense::query()
                ->where(
                    'status',
                    'Cancelled'
                )
                ->whereBetween(
                    'expense_date',
                    [
                        $dateStart,
                        $dateEnd,
                    ]
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | PERIOD LABEL
        |--------------------------------------------------------------------------
        */

        if ($startDate->format('Y-m-d') === '2000-01-01'
            && !$hasStartDate
        ) {

            $periodLabel =
                'All Available Records';

        } elseif ($startDate->isSameDay($endDate)) {

            $periodLabel =
                $startDate->format('F d, Y');

        } elseif ($startDate->isSameMonth($endDate)) {

            $periodLabel =
                $startDate->format('F d')
                . ' - '
                . $endDate->format('d, Y');

        } else {

            $periodLabel =
                $startDate->format('M d, Y')
                . ' - '
                . $endDate->format('M d, Y');

        }


        /*
        |--------------------------------------------------------------------------
        | RETURN FINANCE DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard.finance',
            [

                /*
                |--------------------------------------------------------------------------
                | USER
                |--------------------------------------------------------------------------
                */

                'user' =>
                    $user,


                /*
                |--------------------------------------------------------------------------
                | DATE FILTER
                |--------------------------------------------------------------------------
                */

                'startDate' =>
                    $startDate->toDateString(),

                'endDate' =>
                    $endDate->toDateString(),

                'periodLabel' =>
                    $periodLabel,


                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */

                'totalSales' =>
                    $totalSales,

                'salesCount' =>
                    $salesCount,

                'totalAmountReceived' =>
                    $totalAmountReceived,

                'totalChange' =>
                    $totalChange,

                'salesActivity' =>
                    $salesActivity,

                'recentSales' =>
                    $recentSales,

                'paymentMethodSummary' =>
                    $paymentMethodSummary,


                /*
                |--------------------------------------------------------------------------
                | PURCHASES
                |--------------------------------------------------------------------------
                */

                'totalPurchases' =>
                    $totalPurchases,

                'purchaseCount' =>
                    $purchaseCount,

                'purchaseStatusSummary' =>
                    $purchaseStatusSummary,

                'recentPurchases' =>
                    $recentPurchases,


                /*
                |--------------------------------------------------------------------------
                | EXPENSES
                |--------------------------------------------------------------------------
                */

                'totalExpenses' =>
                    $totalExpenses,

                'expenseCount' =>
                    $expenseCount,

                'expenseCategorySummary' =>
                    $expenseCategorySummary,

                'recentExpenses' =>
                    $recentExpenses,


                /*
                |--------------------------------------------------------------------------
                | NET AMOUNT
                |--------------------------------------------------------------------------
                */

                'netAmount' =>
                    $netAmount,


                /*
                |--------------------------------------------------------------------------
                | CASH REMITTANCE
                |--------------------------------------------------------------------------
                */

                'expectedRemittance' =>
                    $expectedRemittance,

                'actualRemittance' =>
                    $actualRemittance,

                'remittanceVariance' =>
                    $remittanceVariance,

                'remittanceCount' =>
                    $remittanceCount,

                'recentRemittances' =>
                    $recentRemittances,


                /*
                |--------------------------------------------------------------------------
                | TRANSACTION MONITORING
                |--------------------------------------------------------------------------
                */

                'cancelledSalesCount' =>
                    $cancelledSalesCount,

                'cancelledPurchasesCount' =>
                    $cancelledPurchasesCount,

                'cancelledExpensesCount' =>
                    $cancelledExpensesCount,

            ]
        );
    }
}