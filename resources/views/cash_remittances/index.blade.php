@extends('layouts.app')

@section('title', 'BiteSync | Cash Remittance')

@section('content')

<div class="inventory-page">

<!-- =========================================================
     CASH REMITTANCE TOPBAR
========================================================== -->

<div class="topbar">

    <div class="page-title">

        <small>
            Finance Management
        </small>

        <h1>
            Cash Remittance
        </h1>

        <p>
            Record, review, and monitor daily cash reconciliation activity.
        </p>

    </div>


    <div class="date-box">

        <span class="date-icon">
            ◷
        </span>

        {{ now()->format('F d, Y') }}

    </div>

</div>


<!-- =========================================================
     SUMMARY CARDS
     SAME FORMAT AS INVENTORY / EXPENSES
========================================================== -->

<section class="inventory-stats">


    <!-- TOTAL REMITTANCES -->

    <div class="inventory-stat">

        <div class="inventory-stat-left">

            <div class="inventory-stat-label">
                TOTAL REMITTANCES
            </div>

            <div class="inventory-stat-note">
                Non-voided remittance records
            </div>

        </div>


        <div class="inventory-stat-right">

            <div class="inventory-stat-icon">
                ₱
            </div>

            <div class="inventory-stat-value">

                {{ $totalRemittances ?? 0 }}

            </div>

        </div>

    </div>


    <!-- EXPECTED CASH -->

    <div class="inventory-stat">

        <div class="inventory-stat-left">

            <div class="inventory-stat-label">
                EXPECTED CASH
            </div>

            <div class="inventory-stat-note">
                Total expected remittance
            </div>

        </div>


        <div class="inventory-stat-right">

            <div class="inventory-stat-icon">
                ₱
            </div>

            <div class="inventory-stat-value inventory-money">

                ₱{{ number_format(
                    (float) ($totalExpected ?? 0),
                    2
                ) }}

            </div>

        </div>

    </div>


    <!-- ACTUAL CASH -->

    <div class="inventory-stat">

        <div class="inventory-stat-left">

            <div class="inventory-stat-label">
                ACTUAL CASH
            </div>

            <div class="inventory-stat-note">
                Total cash actually remitted
            </div>

        </div>


        <div class="inventory-stat-right">

            <div class="inventory-stat-icon">
                ₱
            </div>

            <div class="inventory-stat-value inventory-money">

                ₱{{ number_format(
                    (float) ($totalActual ?? 0),
                    2
                ) }}

            </div>

        </div>

    </div>


    <!-- CASH VARIANCE -->

    @php

        $summaryVariance =
            (float) ($totalVariance ?? 0);

        if (abs($summaryVariance) < 0.01) {

            $summaryVarianceClass = 'variance-balanced';

        } elseif ($summaryVariance < 0) {

            $summaryVarianceClass = 'variance-shortage';

        } else {

            $summaryVarianceClass = 'variance-excess';

        }

    @endphp


    <div class="inventory-stat">

        <div class="inventory-stat-left">

            <div class="inventory-stat-label">
                CASH VARIANCE
            </div>

            <div class="inventory-stat-note">
                Difference between actual and expected
            </div>

        </div>


        <div class="inventory-stat-right">

            <div class="inventory-stat-icon">
                ≈
            </div>

            <div class="
                inventory-stat-value
                inventory-money
                {{ $summaryVarianceClass }}
            ">

                @if ($summaryVariance < 0)
                    −
                @elseif ($summaryVariance > 0)
                    +
                @endif

                ₱{{ number_format(
                    abs($summaryVariance),
                    2
                ) }}

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     CASH REMITTANCE RECORDS PANEL
========================================================== -->

<div class="inventory-panel">


    <!-- =====================================================
         PANEL HEADER
    ====================================================== -->

    <div class="inventory-panel-header">

        <div>

            <div class="inventory-panel-title">
                Cash Remittance Records
            </div>

            <div class="inventory-panel-subtitle">
                Search and monitor recorded cash reconciliation transactions.
            </div>

        </div>


        <a
            href="{{ route('cash-remittances.create') }}"
            class="inventory-add-button"
        >

            <span>
                +
            </span>

            Add Remittance

        </a>

    </div>


    <!-- =====================================================
         SEARCH / FILTER
    ====================================================== -->

    <form
        method="GET"
        action="{{ route('cash-remittances.index') }}"
        class="inventory-filters"
    >


        <!-- SEARCH -->

        <div class="inventory-search-wrapper">

            <span class="inventory-search-icon">
                ⌕
            </span>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search remittances..."
            >

        </div>


        <!-- STATUS -->

        <select
            name="status"
            aria-label="Filter by status"
        >

            <option value="">
                All Statuses
            </option>

            @foreach (
                ['Recorded', 'Verified', 'Voided']
                as $status
            )

                <option
                    value="{{ $status }}"
                    @selected(request('status') === $status)
                >
                    {{ $status }}
                </option>

            @endforeach

        </select>


        <!-- DATE FROM -->

        <input
            type="date"
            name="date_from"
            value="{{ request('date_from') }}"
            aria-label="Date from"
            class="cash-date-filter"
        >


        <!-- DATE TO -->

        <input
            type="date"
            name="date_to"
            value="{{ request('date_to') }}"
            aria-label="Date to"
            class="cash-date-filter"
        >


        <!-- FILTER -->

        <button
            type="submit"
            class="inventory-filter-button"
        >
            Filter
        </button>


        <!-- CLEAR -->

        @if(request()->hasAny([
            'search',
            'status',
            'date_from',
            'date_to'
        ]))

            <a
                href="{{ route('cash-remittances.index') }}"
                class="inventory-clear-button"
            >
                Clear
            </a>

        @endif

    </form>


    <!-- =====================================================
         CASH REMITTANCE TABLE
====================================================== -->

    <div class="inventory-table-wrapper">

        <table class="inventory-table">

            <thead>

                <tr>

                    <th>
                        Date
                    </th>

                    <th>
                        Expected
                    </th>

                    <th>
                        Actual
                    </th>

                    <th>
                        Variance
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Recorded By
                    </th>

                    <th class="inventory-actions-header">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse (
                    ($remittances ?? collect())
                    as $remittance
                )


                    @php

                        $variance =
                            (float) ($remittance->variance ?? 0);

                        if (abs($variance) < 0.01) {

                            $varianceClass =
                                'variance-balanced';

                        } elseif ($variance < 0) {

                            $varianceClass =
                                'variance-shortage';

                        } else {

                            $varianceClass =
                                'variance-excess';

                        }

                        $statusClass = match (
                            $remittance->status
                        ) {

                            'Recorded' => 'recorded',

                            'Verified' => 'verified',

                            'Voided' => 'voided',

                            default => 'voided',

                        };

                    @endphp


                    <tr>


                        <!-- =================================================
                             DATE
                        ================================================== -->

                        <td>

                            @if ($remittance->remittance_date)

                                <span class="expense-date">

                                    {{ $remittance
                                        ->remittance_date
                                        ->format('M d, Y') }}

                                </span>

                            @else

                                <span class="inventory-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <!-- =================================================
                             EXPECTED
                        ================================================== -->

                        <td>

                            <span class="inventory-price">

                                ₱{{ number_format(
                                    (float) (
                                        $remittance->expected_amount
                                        ?? 0
                                    ),
                                    2
                                ) }}

                            </span>

                        </td>


                        <!-- =================================================
                             ACTUAL
                        ================================================== -->

                        <td>

                            <span class="inventory-price actual-amount">

                                ₱{{ number_format(
                                    (float) (
                                        $remittance->actual_amount
                                        ?? 0
                                    ),
                                    2
                                ) }}

                            </span>

                        </td>


                        <!-- =================================================
                             VARIANCE
                        ================================================== -->

                        <td>

                            <span class="
                                inventory-price
                                {{ $varianceClass }}
                            ">

                                @if ($variance < 0)
                                    −
                                @elseif ($variance > 0)
                                    +
                                @endif

                                ₱{{ number_format(
                                    abs($variance),
                                    2
                                ) }}

                            </span>

                        </td>


                        <!-- =================================================
                             STATUS
                        ================================================== -->

                        <td>

                            <span class="
                                cash-status
                                {{ $statusClass }}
                            ">

                                {{ $remittance->status }}

                            </span>

                        </td>


                        <!-- =================================================
                             RECORDED BY
                        ================================================== -->

                        <td>

                            @if ($remittance->user)

                                <span class="expense-recorder">

                                    {{ $remittance->user->name
                                        ?? '—' }}

                                </span>

                            @else

                                <span class="inventory-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <!-- =================================================
                             ACTIONS
                        ================================================== -->

                        <td>

                            <div class="inventory-table-actions">


                                <!-- VIEW -->

                                <a
                                    href="{{ route(
                                        'cash-remittances.show',
                                        $remittance
                                    ) }}"
                                    class="expense-view-button"
                                >

                                    <span>
                                        ◉
                                    </span>

                                    View

                                </a>


                                <!-- EDIT -->

                                <a
                                    href="{{ route(
                                        'cash-remittances.edit',
                                        $remittance
                                    ) }}"
                                    class="inventory-edit-button"
                                >

                                    <span>
                                        ✎
                                    </span>

                                    Edit

                                </a>


                                <!-- DELETE -->

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'cash-remittances.destroy',
                                        $remittance
                                    ) }}"
                                    onsubmit="return confirm(
                                        'Are you sure you want to delete this cash remittance?'
                                    );"
                                    class="expense-delete-form"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="expense-delete-button"
                                    >

                                        <span>
                                            ×
                                        </span>

                                        Delete

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                @empty


                    <!-- =================================================
                         EMPTY STATE
                    ================================================== -->

                    <tr>

                        <td
                            colspan="7"
                            class="inventory-empty-state"
                        >

                            <div class="inventory-empty-icon">
                                ₱
                            </div>


                            <div class="inventory-empty-title">
                                No cash remittance records found
                            </div>


                            <div class="inventory-empty-description">

                                @if (
                                    request('search') ||
                                    request('status') ||
                                    request('date_from') ||
                                    request('date_to')
                                )

                                    Try changing your search or filter.

                                @else

                                    Your cash remittance records will appear here.

                                @endif

                            </div>

                        </td>

                    </tr>


                @endforelse

            </tbody>

        </table>

    </div>


    <!-- =====================================================
         PAGINATION
    ====================================================== -->

    @if(
        isset($remittances) &&
        method_exists($remittances, 'links') &&
        $remittances->hasPages()
    )

        <div class="inventory-pagination-wrapper">

            {{ $remittances->links() }}

        </div>

    @endif

</div>

</div>

@endsection


@push('styles')

<style>

/* =========================================================
   CASH REMITTANCE PAGE
   USES INVENTORY / EXPENSE LAYOUT
========================================================= */

.inventory-page {
    width: 100%;
}


/* =========================================================
   PAGE TITLE
========================================================= */

.inventory-page .page-title h1 {

    font-size:
        clamp(1.6rem, 2vw, 1.9rem);

    line-height: 1.15;

    letter-spacing: -0.04rem;
}


/* =========================================================
   SUMMARY CARDS
========================================================= */

.inventory-stats {

    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 17px;

    margin-bottom: 17px;
}


.inventory-stat {

    min-height: 125px;

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

    padding:
        16px
        17px
        15px;

    border:
        1px solid var(--border);

    border-radius: 15px;

    background: white;

    box-shadow:
        0 5px 18px
        rgba(43, 31, 23, 0.045);

    position: relative;

    overflow: hidden;
}


/* =========================================================
   LEFT ACCENT
========================================================= */

.inventory-stat::before {

    content: "";

    position: absolute;

    top: 0;
    left: 0;
    bottom: 0;

    width: 3px;

    background:
        linear-gradient(
            180deg,
            var(--orange),
            #e2a16c
        );
}


/* =========================================================
   LEFT SIDE
========================================================= */

.inventory-stat-left {

    min-width: 0;

    flex: 1;

    display: flex;

    flex-direction: column;

    align-items: flex-start;

    justify-content: flex-start;

    align-self: stretch;

    padding-left: 1px;

    padding-top: 2px;
}


/* =========================================================
   LABEL
========================================================= */

.inventory-stat-label {

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1.3;

    font-weight: 800;

    letter-spacing: 0.045rem;

    white-space: nowrap;
}


/* =========================================================
   NOTE
========================================================= */

.inventory-stat-note {

    margin-top: 58px;

    color: #9d958f;

    font-size: 0.6875rem;

    line-height: 1.4;

    max-width: 155px;
}


/* =========================================================
   RIGHT SIDE
========================================================= */

.inventory-stat-right {

    min-width: 82px;

    display: flex;

    flex-direction: column;

    align-items: flex-end;

    justify-content: flex-start;

    padding-top: 3px;

    flex-shrink: 0;
}


/* =========================================================
   ICON
========================================================= */

.inventory-stat-icon {

    width: 34px;

    height: 34px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 9px;

    background:
        linear-gradient(
            135deg,
            #fbf1e7,
            #f4e3d4
        );

    color: var(--orange);

    font-size: 0.8125rem;

    font-weight: 800;
}


/* =========================================================
   VALUE
========================================================= */

.inventory-stat-value {

    margin-top: 13px;

    color: var(--dark);

    font-size:
        clamp(1.45rem, 1.8vw, 1.75rem);

    line-height: 1;

    font-weight: 800;

    text-align: right;

    white-space: nowrap;
}


/* =========================================================
   MONEY
========================================================= */

.inventory-money {

    font-size:
        clamp(1rem, 1.3vw, 1.25rem);

    letter-spacing: -0.02rem;
}


/* =========================================================
   VARIANCE COLORS
========================================================= */

.variance-balanced {

    color: #5d8b67 !important;
}


.variance-shortage {

    color: #b95d56 !important;
}


.variance-excess {

    color: #637f9f !important;
}


/* =========================================================
   PANEL
========================================================= */

.inventory-panel {

    background: white;

    border:
        1px solid var(--border);

    border-radius: 15px;

    overflow: hidden;

    box-shadow:
        0 4px 16px
        rgba(43, 31, 23, 0.035);
}


/* =========================================================
   PANEL HEADER
========================================================= */

.inventory-panel-header {

    min-height: 65px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 17px;

    padding:
        14px
        18px;

    border-bottom:
        1px solid var(--border);
}


.inventory-panel-title {

    color: var(--dark);

    font-size: 0.875rem;

    line-height: 1.3;

    font-weight: 700;
}


.inventory-panel-subtitle {

    margin-top: 3px;

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1.4;
}


/* =========================================================
   ADD BUTTON
========================================================= */

.inventory-add-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 5px;

    min-height: 32px;

    padding:
        0
        10px;

    border-radius: 8px;

    background:
        linear-gradient(
            135deg,
            var(--orange),
            var(--orange-dark)
        );

    color: white;

    text-decoration: none;

    font-size: 0.6875rem;

    line-height: 1.2;

    font-weight: 700;

    box-shadow:
        0 3px 9px
        rgba(168, 95, 40, 0.12);

    transition:
        background 0.18s ease,
        box-shadow 0.18s ease;
}


.inventory-add-button:hover {

    color: white;

    background:
        linear-gradient(
            135deg,
            var(--orange-dark),
            var(--orange-dark)
        );

    box-shadow:
        0 4px 10px
        rgba(168, 95, 40, 0.15);
}


.inventory-add-button span {

    font-size: 0.8125rem;

    line-height: 1;
}


/* =========================================================
   FILTERS
========================================================= */

.inventory-filters {

    display: flex;

    align-items: center;

    gap: 8px;

    padding:
        12px
        18px;

    background: var(--card-soft);

    border-bottom:
        1px solid var(--border);
}


.inventory-search-wrapper {

    flex: 1;

    position: relative;

    min-width: 180px;
}


.inventory-search-wrapper input {

    width: 100%;

    height: 35px;

    padding:
        0
        11px
        0
        34px;

    border:
        1px solid var(--border);

    border-radius: 8px;

    background: white;

    color: var(--text);

    font-family: inherit;

    font-size: 0.75rem;

    outline: none;

    transition:
        border-color 0.18s ease,
        box-shadow 0.18s ease;
}


.inventory-search-wrapper input:focus {

    border-color: #d5a77d;

    box-shadow:
        0 0 0 3px
        rgba(196, 122, 58, 0.07);
}


.inventory-search-wrapper input::placeholder {

    color: #aaa19a;
}


.inventory-search-icon {

    position: absolute;

    left: 11px;

    top: 50%;

    transform:
        translateY(-50%);

    color: var(--muted);

    font-size: 0.875rem;

    pointer-events: none;
}


/* =========================================================
   FILTER SELECTS
========================================================= */

.inventory-filters select {

    height: 35px;

    min-width: 125px;

    padding:
        0
        10px;

    border:
        1px solid var(--border);

    border-radius: 8px;

    background: white;

    color: var(--text);

    font-family: inherit;

    font-size: 0.75rem;

    outline: none;

    cursor: pointer;
}


/* =========================================================
   DATE FILTERS
========================================================= */

.cash-date-filter {

    height: 35px;

    min-width: 135px;

    padding:
        0
        10px;

    border:
        1px solid var(--border);

    border-radius: 8px;

    background: white;

    color: var(--text);

    font-family: inherit;

    font-size: 0.75rem;

    outline: none;

    cursor: pointer;
}


.cash-date-filter:focus,
.inventory-filters select:focus {

    border-color: #d5a77d;

    box-shadow:
        0 0 0 3px
        rgba(196, 122, 58, 0.07);
}


/* =========================================================
   FILTER / CLEAR BUTTON
========================================================= */

.inventory-filter-button,
.inventory-clear-button {

    height: 35px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding:
        0
        11px;

    border-radius: 8px;

    font-family: inherit;

    font-size: 0.75rem;

    font-weight: 700;

    line-height: 1.2;

    text-decoration: none;

    cursor: pointer;

    white-space: nowrap;
}


.inventory-filter-button {

    border: none;

    background: var(--dark);

    color: white;

    transition:
        background-color 0.18s ease;
}


.inventory-filter-button:hover {

    background: var(--dark-soft);
}


.inventory-clear-button {

    border:
        1px solid var(--border);

    background: white;

    color: var(--muted);

    transition:
        background-color 0.18s ease,
        color 0.18s ease;
}


.inventory-clear-button:hover {

    background: #faf7f3;

    color: var(--dark);
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.inventory-table-wrapper {

    width: 100%;

    overflow-x: auto;
}


/* =========================================================
   TABLE
========================================================= */

.inventory-table {

    width: 100%;

    min-width: 1080px;

    border-collapse: collapse;
}


.inventory-table th {

    padding:
        10px
        12px;

    background: #fbf9f6;

    color: var(--muted);

    border-bottom:
        1px solid var(--border);

    text-align: left;

    font-size: 0.625rem;

    line-height: 1.3;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: 0.04rem;

    white-space: nowrap;
}


.inventory-table td {

    padding:
        11px
        12px;

    color: #625951;

    border-bottom:
        1px solid #f0ebe6;

    font-size: 0.75rem;

    line-height: 1.4;

    vertical-align: middle;
}


.inventory-table tbody tr {

    background: white;
}


.inventory-table tbody tr:hover {

    background: #fdfaf7;
}


/* =========================================================
   DATE
========================================================= */

.expense-date {

    color: #625951;

    font-size: 0.75rem;

    line-height: 1.4;

    font-weight: 600;

    white-space: nowrap;
}


/* =========================================================
   RECORDED BY
========================================================= */

.expense-recorder {

    color: #766b63;

    font-size: 0.6875rem;

    white-space: nowrap;
}


/* =========================================================
   ACTUAL AMOUNT
========================================================= */

.actual-amount {

    color: #5d8b67 !important;
}


/* =========================================================
   MUTED
========================================================= */

.inventory-muted {

    color: #a39b95;

    font-size: 0.75rem;
}


/* =========================================================
   PRICE
========================================================= */

.inventory-price {

    color: var(--dark);

    font-size: 0.75rem;

    font-weight: 800;

    white-space: nowrap;
}


/* =========================================================
   STATUS
========================================================= */

.cash-status {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 23px;

    padding:
        4px
        7px;

    border-radius: 6px;

    font-size: 0.625rem;

    line-height: 1.2;

    font-weight: 800;

    white-space: nowrap;
}


.cash-status.recorded {

    background: #edf6ef;

    color: #5d8b67;
}


.cash-status.verified {

    background: #edf2f7;

    color: #637f9f;
}


.cash-status.voided {

    background: #fbeeed;

    color: #b95d56;
}


/* =========================================================
   ACTION HEADER
========================================================= */

.inventory-actions-header {

    text-align: center !important;
}


/* =========================================================
   ACTIONS
========================================================= */

.inventory-table-actions {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 4px;

    white-space: nowrap;
}


.inventory-table-actions form {

    margin: 0;

    padding: 0;
}


/* =========================================================
   VIEW BUTTON
========================================================= */

.expense-view-button {

    height: 27px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 3px;

    padding:
        0
        7px;

    border:
        1px solid #d9d0c8;

    border-radius: 7px;

    background: white;

    color: #75685d;

    text-decoration: none;

    font-family: inherit;

    font-size: 0.625rem;

    line-height: 1.2;

    font-weight: 700;

    cursor: pointer;

    transition:
        background-color 0.18s ease,
        border-color 0.18s ease,
        color 0.18s ease;
}


.expense-view-button:hover {

    background: #faf6f2;

    border-color: #cdbba9;

    color: #59483b;
}


.expense-view-button span {

    font-size: 0.625rem;

    line-height: 1;
}


/* =========================================================
   EDIT BUTTON
========================================================= */

.inventory-edit-button {

    height: 27px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 3px;

    padding:
        0
        7px;

    border:
        1px solid #e1d4c8;

    border-radius: 7px;

    background: white;

    color: #7d5b42;

    text-decoration: none;

    font-family: inherit;

    font-size: 0.625rem;

    line-height: 1.2;

    font-weight: 700;

    cursor: pointer;

    transition:
        background-color 0.18s ease,
        border-color 0.18s ease,
        color 0.18s ease;
}


.inventory-edit-button:hover {

    background: #faf5ef;

    border-color: #cdb49f;

    color: #68482f;
}


.inventory-edit-button span {

    font-size: 0.6875rem;

    line-height: 1;
}


/* =========================================================
   DELETE BUTTON
========================================================= */

.expense-delete-button {

    height: 27px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 3px;

    padding:
        0
        7px;

    border:
        1px solid #e1cfcb;

    border-radius: 7px;

    background: white;

    color: #a15d50;

    font-family: inherit;

    font-size: 0.625rem;

    line-height: 1.2;

    font-weight: 700;

    cursor: pointer;

    transition:
        background-color 0.18s ease,
        border-color 0.18s ease,
        color 0.18s ease;
}


.expense-delete-button:hover {

    background: #fff7f5;

    border-color: #dfbcb4;

    color: #8d463b;
}


.expense-delete-button span {

    font-size: 0.75rem;

    line-height: 1;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.inventory-empty-state {

    padding:
        55px
        20px !important;

    text-align: center !important;
}


.inventory-empty-icon {

    width: 46px;

    height: 46px;

    margin:
        0
        auto
        11px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 12px;

    background: var(--orange-light);

    color: var(--orange);

    font-size: 1.05rem;
}


.inventory-empty-title {

    color: var(--dark);

    font-size: 0.8125rem;

    font-weight: 700;
}


.inventory-empty-description {

    margin-top: 4px;

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1.4;
}


/* =========================================================
   PAGINATION
========================================================= */

.inventory-pagination-wrapper {

    padding:
        13px
        18px;

    border-top:
        1px solid var(--border);
}


.inventory-pagination-wrapper nav {

    display: flex;

    justify-content: center;
}


.inventory-pagination-wrapper svg {

    width: 14px;

    height: 14px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {

    .inventory-stats {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


@media (max-width: 900px) {

    .inventory-filters {

        flex-wrap: wrap;
    }


    .inventory-search-wrapper {

        flex-basis: 100%;
    }

}


@media (max-width: 700px) {

    .inventory-stats {

        grid-template-columns: 1fr;
    }


    .inventory-stat {

        min-height: 115px;
    }


    .inventory-stat-left {

        padding-top: 1px;
    }


    .inventory-stat-right {

        min-width: 72px;

        padding-top: 3px;
    }


    .inventory-stat-value {

        font-size: 1.45rem;
    }


    .inventory-money {

        font-size: 1.05rem;
    }


    .inventory-stat-note {

        margin-top: 55px;
    }


    .inventory-panel-header {

        align-items: flex-start;

        flex-direction: column;
    }


    .inventory-add-button {

        width: 100%;
    }


    .inventory-filters {

        align-items: stretch;

        flex-direction: column;
    }


    .inventory-search-wrapper {

        width: 100%;

        flex-basis: auto;
    }


    .inventory-filters select,
    .cash-date-filter,
    .inventory-filter-button,
    .inventory-clear-button {

        width: 100%;
    }


    .inventory-table-actions {

        justify-content: flex-start;
    }

}

</style>

@endpush