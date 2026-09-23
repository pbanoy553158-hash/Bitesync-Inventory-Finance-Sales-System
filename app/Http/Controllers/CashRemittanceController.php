<?php

namespace App\Http\Controllers;

use App\Models\CashRemittance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashRemittanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Display Cash Remittances
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $query = CashRemittance::with('user')
            ->orderByDesc('remittance_date')
            ->orderByDesc('id');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery->where('name', 'like', "%{$search}%");

                    });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }


        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'remittance_date',
                '>=',
                $request->date_from
            );

        }

        if ($request->filled('date_to')) {

            $query->whereDate(
                'remittance_date',
                '<=',
                $request->date_to
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Paginated Records
        |--------------------------------------------------------------------------
        */

        $remittances = $query
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        |
        | Voided remittances are excluded from financial totals.
        |
        */

        $summaryQuery = CashRemittance::query()
            ->where('status', '!=', 'Voided');

        if ($request->filled('date_from')) {

            $summaryQuery->whereDate(
                'remittance_date',
                '>=',
                $request->date_from
            );

        }

        if ($request->filled('date_to')) {

            $summaryQuery->whereDate(
                'remittance_date',
                '<=',
                $request->date_to
            );

        }


        $totalRemittances = (clone $summaryQuery)->count();

        $totalExpected = (float) (clone $summaryQuery)
            ->sum('expected_amount');

        $totalActual = (float) (clone $summaryQuery)
            ->sum('actual_amount');

        $totalVariance = $totalActual - $totalExpected;


        return view(
            'cash_remittances.index',
            compact(
                'remittances',
                'totalRemittances',
                'totalExpected',
                'totalActual',
                'totalVariance'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Create Form
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        return view('cash_remittances.create');
    }


    /*
    |--------------------------------------------------------------------------
    | Store Cash Remittance
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'remittance_date' => [
                'required',
                'date',
            ],

            'expected_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'actual_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],

            'status' => [
                'required',
                'in:Recorded,Verified,Voided',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Calculate Variance
        |--------------------------------------------------------------------------
        |
        | Variance = Actual Amount - Expected Amount
        |
        | Positive value  = over
        | Negative value  = short
        | Zero             = balanced
        |
        */

        $validated['variance'] =
            (float) $validated['actual_amount']
            -
            (float) $validated['expected_amount'];


        /*
        |--------------------------------------------------------------------------
        | Record Current User
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        $validated['user_id'] = $user->id;

        /*
        |--------------------------------------------------------------------------
        | Create Record
        |--------------------------------------------------------------------------
        */

        CashRemittance::create($validated);


        return redirect()
            ->route('cash-remittances.index')
            ->with(
                'success',
                'Cash remittance recorded successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Display Single Cash Remittance
    |--------------------------------------------------------------------------
    */

    public function show(
        CashRemittance $cashRemittance
    ): View {

        $cashRemittance->load('user');

        return view(
            'cash_remittances.show',
            compact('cashRemittance')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Edit Form
    |--------------------------------------------------------------------------
    */

    public function edit(
        CashRemittance $cashRemittance
    ): View {

        return view(
            'cash_remittances.edit',
            compact('cashRemittance')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Cash Remittance
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        CashRemittance $cashRemittance
    ): RedirectResponse {

        $validated = $request->validate([
            'remittance_date' => [
                'required',
                'date',
            ],

            'expected_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'actual_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],

            'status' => [
                'required',
                'in:Recorded,Verified,Voided',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Recalculate Variance
        |--------------------------------------------------------------------------
        */

        $validated['variance'] =
            (float) $validated['actual_amount']
            -
            (float) $validated['expected_amount'];


        /*
        |--------------------------------------------------------------------------
        | Update Record
        |--------------------------------------------------------------------------
        */

        $cashRemittance->update($validated);


        return redirect()
            ->route('cash-remittances.index')
            ->with(
                'success',
                'Cash remittance updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Cash Remittance
    |--------------------------------------------------------------------------
    */

    public function destroy(
        CashRemittance $cashRemittance
    ): RedirectResponse {

        $cashRemittance->delete();


        return redirect()
            ->route('cash-remittances.index')
            ->with(
                'success',
                'Cash remittance deleted successfully.'
            );
    }
}