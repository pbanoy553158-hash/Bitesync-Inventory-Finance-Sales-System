@extends('layouts.app')

@section('title', 'Finance Dashboard')

@section('content')

@php

    $salesActivityJson =
        json_encode($salesActivity ?? []);

    $paymentMethodSummaryJson =
        json_encode($paymentMethodSummary ?? []);

    $expenseCategorySummaryJson =
        json_encode($expenseCategorySummary ?? []);

@endphp


<style>

/* ============================================================
   FINANCE DASHBOARD
   SAME VISUAL SYSTEM AS PROCUREMENT DASHBOARD
   FINANCE DATA ONLY
   ============================================================ */

.dashboard-page {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding-bottom: 30px;
}


/* ============================================================
   HEADER
   ============================================================ */

.dashboard-page .topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 17px;
}


.dashboard-page .page-title small {
    display: block;
    margin-bottom: 5px;
    color: #a9825b;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
}


.dashboard-page .page-title h1 {
    margin: 0;
    color: #241a14;
    font-size: 29px;
    font-weight: 700;
    line-height: 1.15;
    letter-spacing: -.045rem;
}


.dashboard-page .page-title p {
    margin: 6px 0 0;
    color: #8b8179;
    font-size: 12px;
    line-height: 1.5;
    font-weight: 400;
}


.dashboard-page .date-box {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    min-width: 145px;
    padding: 9px 12px;
    border: 1px solid #e4dcd4;
    border-radius: 10px;
    background: white;
    color: #8b8179;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 3px 12px rgba(43,31,23,.025);
}


.dashboard-page .date-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    border-radius: 8px;
    background: #f4e4d4;
    color: #c47a3a;
    font-size: 17px;
}


/* ============================================================
   FINANCE NOTICE
   ============================================================ */

.finance-notice {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 17px;
    padding: 11px 13px;
    border: 1px solid #eadfd4;
    border-radius: 11px;
    background: #fcfaf7;
    color: #756b63;
    font-size: .68rem;
    line-height: 1.45;
}


.finance-notice-icon {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 8px;
    background: #f4e4d4;
    color: #c47a3a;
    font-size: .68rem;
    font-weight: 800;
}


.finance-notice strong {
    color: #4f443b;
}


/* ============================================================
   KPI CARDS
   ============================================================ */

.dashboard-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 17px;
    margin-bottom: 17px;
}


.dashboard-kpi {
    min-height: 125px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    padding: 16px 17px 15px;
    border: 1px solid #e4dcd4;
    border-radius: 15px;
    background: white;
    box-shadow: 0 5px 18px rgba(43,31,23,.045);
    position: relative;
    overflow: hidden;
}


.dashboard-kpi::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #c47a3a, #e2a16c);
}


.dashboard-kpi-left {
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


.dashboard-kpi-label {
    color: #8b8179;
    font-size: .6875rem;
    line-height: 1.3;
    font-weight: 800;
    letter-spacing: .045rem;
    white-space: nowrap;
    text-transform: uppercase;
}


.dashboard-kpi-note {
    margin-top: auto;
    padding-top: 10px;
    color: #9d958f;
    font-size: .6875rem;
    line-height: 1.4;
    max-width: 155px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


.dashboard-kpi-right {
    min-width: 88px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: flex-start;
    padding-top: 3px;
    flex-shrink: 0;
}


.dashboard-kpi-icon {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 9px;
    background: linear-gradient(135deg, #fbf1e7, #f4e3d4);
    color: #c47a3a;
    font-size: .72rem;
    font-weight: 800;
}


.dashboard-kpi-icon.green {
    background: #edf6ef;
    color: #5d8b67;
}


.dashboard-kpi-icon.blue {
    background: #edf2f7;
    color: #637f9f;
}


.dashboard-kpi-icon.red {
    background: #fbeeed;
    color: #b95d56;
}


.dashboard-kpi-value {
    margin-top: 13px;
    color: #241a14;
    font-size: clamp(1.45rem, 1.8vw, 1.75rem);
    line-height: 1;
    font-weight: 800;
    text-align: right;
    white-space: nowrap;
}


.dashboard-kpi-value.money {
    font-size: clamp(1rem, 1.3vw, 1.25rem);
    letter-spacing: -.02rem;
}


/* ============================================================
   MAIN ANALYTICS
   ============================================================ */

.dashboard-main-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1.7fr)
        minmax(310px, .85fr);
    gap: 17px;
    margin-bottom: 17px;
}


/* ============================================================
   OPERATIONAL GRID
   ============================================================ */

.dashboard-operational-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1.35fr)
        minmax(0, 1fr);
    gap: 17px;
    margin-bottom: 17px;
}


/* ============================================================
   PANELS
   ============================================================ */

.dashboard-panel {
    background: white;
    border: 1px solid #e4dcd4;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(43,31,23,.035);
}


.dashboard-panel-header {
    min-height: 65px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 14px 18px;
    border-bottom: 1px solid #e4dcd4;
}


.dashboard-panel-heading h2 {
    margin: 0;
    color: #241a14;
    font-size: .875rem;
    line-height: 1.3;
    font-weight: 700;
}


.dashboard-panel-heading p {
    margin: 3px 0 0;
    color: #8b8179;
    font-size: .6875rem;
    line-height: 1.4;
    font-weight: 400;
}


.dashboard-panel-body {
    padding: 18px;
}


/* ============================================================
   CHARTS
   ============================================================ */

.dashboard-chart {
    width: 100%;
    min-height: 310px;
    position: relative;
}


.dashboard-chart-small {
    min-height: 300px;
}


/* ============================================================
   FINANCIAL SUMMARY
   ============================================================ */

.finance-summary {
    display: grid;
    gap: 13px;
}


.finance-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f0ebe6;
}


.finance-row:last-child {
    padding-bottom: 0;
    border-bottom: 0;
}


.finance-label {
    color: #8b8179;
    font-size: .72rem;
}


.finance-value {
    color: #241a14;
    font-size: .82rem;
    font-weight: 800;
    text-align: right;
}


.finance-value.orange {
    color: #a85f28;
}


.finance-value.green {
    color: #5d8b67;
}


.finance-value.red {
    color: #b95d56;
}


.finance-value.blue {
    color: #637f9f;
}


.finance-divider {
    height: 1px;
    background: #e4dcd4;
    margin: 3px 0;
}


.finance-highlight {
    padding: 14px;
    border-radius: 11px;
    background: #fcfaf7;
    border: 1px solid #eee6de;
}


.finance-highlight-label {
    color: #8b8179;
    font-size: .65rem;
    font-weight: 800;
    letter-spacing: .04rem;
    text-transform: uppercase;
}


.finance-highlight-value {
    margin-top: 6px;
    color: #241a14;
    font-size: 1.35rem;
    font-weight: 800;
}


/* ============================================================
   ACTIVITY
   ============================================================ */

.dashboard-activity-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1.35fr)
        minmax(0, 1fr);
    gap: 17px;
    margin-bottom: 17px;
}


/* ============================================================
   TABLE
   ============================================================ */

.dashboard-table-wrapper {
    width: 100%;
    overflow-x: auto;
}


.dashboard-table {
    width: 100%;
    min-width: 560px;
    border-collapse: collapse;
}


.dashboard-table th {
    padding: 10px 12px;
    background: #fbf9f6;
    color: #8b8179;
    border-bottom: 1px solid #e4dcd4;
    text-align: left;
    font-size: .625rem;
    line-height: 1.3;
    font-weight: 800;
    letter-spacing: .04rem;
    text-transform: uppercase;
    white-space: nowrap;
}


.dashboard-table td {
    padding: 11px 12px;
    color: #625951;
    border-bottom: 1px solid #f0ebe6;
    font-size: .72rem;
    line-height: 1.4;
    vertical-align: middle;
}


.dashboard-table tbody tr:last-child td {
    border-bottom: 0;
}


.dashboard-number {
    color: #a85f28;
    font-weight: 700;
}


.dashboard-amount {
    color: #241a14;
    font-weight: 800;
    white-space: nowrap;
}


/* ============================================================
   STATUS
   ============================================================ */

.dashboard-status {
    display: inline-flex;
    align-items: center;
    min-height: 23px;
    padding: 0 7px;
    border-radius: 7px;
    font-size: .6rem;
    line-height: 1.2;
    font-weight: 800;
    white-space: nowrap;
}


.dashboard-status.completed {
    background: #edf6ef;
    color: #5d8b67;
}


.dashboard-status.cash {
    background: #edf6ef;
    color: #5d8b67;
}


.dashboard-status.pending {
    background: #fbf1e3;
    color: #b9823e;
}


.dashboard-status.cancelled {
    background: #fbeeed;
    color: #b95d56;
}


.dashboard-status.card {
    background: #edf2f7;
    color: #637f9f;
}


.dashboard-status.gcash {
    background: #f4e4d4;
    color: #a85f28;
}


.dashboard-status.other {
    background: #edf2f7;
    color: #637f9f;
}


/* ============================================================
   EMPTY STATE
   ============================================================ */

.dashboard-empty {
    padding: 42px 18px;
    text-align: center;
}


.dashboard-empty-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    border-radius: 11px;
    background: #f4e4d4;
    color: #c47a3a;
    font-size: 15px;
}


.dashboard-empty-title {
    color: #625951;
    font-size: .8rem;
    font-weight: 700;
}


.dashboard-empty-text {
    margin-top: 4px;
    color: #9d958f;
    font-size: .68rem;
}


/* ============================================================
   RESPONSIVE
   ============================================================ */

@media (max-width: 1200px) {

    .dashboard-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-main-grid {
        grid-template-columns: 1fr;
    }

}


@media (max-width: 850px) {

    .dashboard-operational-grid,
    .dashboard-activity-grid {
        grid-template-columns: 1fr;
    }

}


@media (max-width: 700px) {

    .dashboard-page .topbar {
        flex-direction: column;
        gap: 12px;
    }

    .dashboard-page .date-box {
        align-self: flex-start;
    }

    .dashboard-kpis {
        grid-template-columns: 1fr;
    }

    .dashboard-kpi {
        min-height: 115px;
    }

    .dashboard-kpi-right {
        min-width: 80px;
    }

    .dashboard-kpi-value {
        font-size: 1.45rem;
    }

    .dashboard-kpi-value.money {
        font-size: 1.05rem;
    }

    .dashboard-panel-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .dashboard-panel-body {
        padding: 15px;
    }

    .finance-notice {
        align-items: flex-start;
    }

}


@media (max-width: 480px) {

    .dashboard-page .page-title h1 {
        font-size: 23px;
    }

    .dashboard-kpi {
        min-height: 115px;
    }

    .dashboard-kpi-note {
        max-width: 145px;
    }

}

</style>


<div class="dashboard-page">


    {{-- ============================================================
         HEADER
         ============================================================ --}}

    <div class="topbar">

        <div class="page-title">

            <small>
                Finance Overview
            </small>

            <h1>
                Dashboard
            </h1>

            <p>
                Monitor sales, expenses, collections, purchases, and cash activity.
            </p>

        </div>


        <div class="date-box">

            <span class="date-icon">
                ◷
            </span>

            {{ now()->format('F d, Y') }}

        </div>

    </div>



    {{-- ============================================================
         FINANCE NOTICE
         ============================================================ --}}

    <div class="finance-notice">

        <div class="finance-notice-icon">
            ₱
        </div>

        <div>

            <strong>
                Finance Workspace
            </strong>

            &nbsp; Monitor financial transactions, sales collections,
            expenses, purchases, and cash remittance activity from your
            assigned finance modules.

        </div>

    </div>



    {{-- ============================================================
         KPI CARDS
         ============================================================ --}}

    <div class="dashboard-kpis">


        {{-- Total Sales --}}

        <div class="dashboard-kpi">

            <div class="dashboard-kpi-left">

                <div class="dashboard-kpi-label">
                    Total Sales
                </div>

                <div class="dashboard-kpi-note">
                    Completed sales
                </div>

            </div>


            <div class="dashboard-kpi-right">

                <div class="dashboard-kpi-icon green">
                    ₱
                </div>

                <div class="dashboard-kpi-value money">
                    ₱{{ number_format(
                        (float) ($totalSales ?? 0),
                        2
                    ) }}
                </div>

            </div>

        </div>



        {{-- Total Expenses --}}

        <div class="dashboard-kpi">

            <div class="dashboard-kpi-left">

                <div class="dashboard-kpi-label">
                    Total Expenses
                </div>

                <div class="dashboard-kpi-note">
                    Recorded expenses
                </div>

            </div>


            <div class="dashboard-kpi-right">

                <div class="dashboard-kpi-icon red">
                    −
                </div>

                <div class="dashboard-kpi-value money">
                    ₱{{ number_format(
                        (float) ($totalExpenses ?? 0),
                        2
                    ) }}
                </div>

            </div>

        </div>



        {{-- Net Amount --}}

        <div class="dashboard-kpi">

            <div class="dashboard-kpi-left">

                <div class="dashboard-kpi-label">
                    Net Amount
                </div>

                <div class="dashboard-kpi-note">
                    Sales less costs
                </div>

            </div>


            <div class="dashboard-kpi-right">

                <div class="dashboard-kpi-icon blue">
                    ₱
                </div>

                <div class="dashboard-kpi-value money">
                    ₱{{ number_format(
                        (float) ($netAmount ?? 0),
                        2
                    ) }}
                </div>

            </div>

        </div>



        {{-- Cash Variance --}}

        <div class="dashboard-kpi">

            <div class="dashboard-kpi-left">

                <div class="dashboard-kpi-label">
                    Cash Variance
                </div>

                <div class="dashboard-kpi-note">
                    Actual vs expected
                </div>

            </div>


            <div class="dashboard-kpi-right">

                @php
                    $variance =
                        (float) ($remittanceVariance ?? 0);
                @endphp

                <div class="dashboard-kpi-icon
                    {{ $variance == 0 ? 'green' : 'red' }}">

                    {{ $variance == 0 ? '✓' : '!' }}

                </div>

                <div class="dashboard-kpi-value money">

                    ₱{{ number_format(
                        abs($variance),
                        2
                    ) }}

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
         MAIN ANALYTICS
         ============================================================ --}}

    <div class="dashboard-main-grid">


        {{-- Sales Activity --}}

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-heading">

                    <h2>
                        Sales Activity
                    </h2>

                    <p>
                        Daily completed sales for the selected period.
                    </p>

                </div>

            </div>


            <div class="dashboard-panel-body">

                <div class="dashboard-chart">

                    <canvas id="salesActivityChart"></canvas>

                </div>

            </div>

        </div>



        {{-- Financial Summary --}}

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-heading">

                    <h2>
                        Financial Summary
                    </h2>

                    <p>
                        Current financial position for the selected period.
                    </p>

                </div>

            </div>


            <div class="dashboard-panel-body">

                <div class="finance-summary">


                    {{-- Completed Sales --}}

                    <div class="finance-row">

                        <span class="finance-label">
                            Completed Sales
                        </span>

                        <span class="finance-value green">
                            {{ number_format(
                                $salesCount ?? 0
                            ) }}
                        </span>

                    </div>


                    <div class="finance-divider"></div>


                    {{-- Net Amount --}}

                    <div class="finance-highlight">

                        <div class="finance-highlight-label">
                            Net Amount
                        </div>

                        <div class="finance-highlight-value">

                            ₱{{ number_format(
                                (float) ($netAmount ?? 0),
                                2
                            ) }}

                        </div>

                    </div>


                    {{-- Purchases --}}

                    <div class="finance-row">

                        <span class="finance-label">
                            Purchases
                        </span>

                        <span class="finance-value orange">
                            ₱{{ number_format(
                                (float) ($totalPurchases ?? 0),
                                2
                            ) }}
                        </span>

                    </div>


                    {{-- Expenses --}}

                    <div class="finance-row">

                        <span class="finance-label">
                            Expenses
                        </span>

                        <span class="finance-value red">
                            ₱{{ number_format(
                                (float) ($totalExpenses ?? 0),
                                2
                            ) }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
         OPERATIONAL GRID
         ============================================================ --}}

    <div class="dashboard-operational-grid">


        {{-- Payment Methods --}}

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-heading">

                    <h2>
                        Payment Methods
                    </h2>

                    <p>
                        Completed sales grouped by payment method.
                    </p>

                </div>

            </div>


            <div class="dashboard-panel-body">

                <div class="dashboard-chart dashboard-chart-small">

                    <canvas id="paymentMethodChart"></canvas>

                </div>

            </div>

        </div>



        {{-- Expense Categories --}}

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-heading">

                    <h2>
                        Expense Categories
                    </h2>

                    <p>
                        Expense distribution for the selected period.
                    </p>

                </div>

            </div>


            <div class="dashboard-panel-body">

                <div class="dashboard-chart dashboard-chart-small">

                    <canvas id="expenseCategoryChart"></canvas>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
         RECENT ACTIVITY
         ============================================================ --}}

    <div class="dashboard-activity-grid">


        {{-- Recent Sales --}}

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-heading">

                    <h2>
                        Recent Sales
                    </h2>

                    <p>
                        Latest completed sales and payment transactions.
                    </p>

                </div>

            </div>


            @if (($recentSales ?? collect())->count())

                <div class="dashboard-table-wrapper">

                    <table class="dashboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Sale
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Payment
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Total
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($recentSales as $sale)

                                @php

                                    $paymentType =
                                        strtolower(
                                            trim(
                                                $sale->payment_method
                                                    ?? 'Other'
                                            )
                                        );

                                    $paymentClass =
                                        match ($paymentType) {

                                            'cash'
                                                => 'cash',

                                            'gcash'
                                                => 'gcash',

                                            'card',
                                            'credit card',
                                            'debit card'
                                                => 'card',

                                            default
                                                => 'other',

                                        };

                                @endphp


                                <tr>

                                    <td>

                                        <span class="dashboard-number">
                                            {{ $sale->sale_number }}
                                        </span>

                                    </td>


                                    <td>

                                        {{ $sale->sale_date
                                            ? $sale->sale_date->format('M d, Y')
                                            : '—'
                                        }}

                                    </td>


                                    <td>

                                        <span class="dashboard-status {{ $paymentClass }}">
                                            {{ $sale->payment_method ?? 'Other' }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="dashboard-status completed">
                                            {{ $sale->status }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="dashboard-amount">

                                            ₱{{ number_format(
                                                (float) $sale->total,
                                                2
                                            ) }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="dashboard-empty">

                    <div class="dashboard-empty-icon">
                        ₱
                    </div>

                    <div class="dashboard-empty-title">
                        No sales recorded yet
                    </div>

                    <div class="dashboard-empty-text">
                        Completed sales will appear here.
                    </div>

                </div>

            @endif

        </div>



        {{-- Recent Expenses --}}

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-heading">

                    <h2>
                        Recent Expenses
                    </h2>

                    <p>
                        Latest recorded business expenses.
                    </p>

                </div>

            </div>


            @if (($recentExpenses ?? collect())->count())

                <div class="dashboard-table-wrapper">

                    <table class="dashboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Description
                                </th>

                                <th>
                                    Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($recentExpenses as $expense)

                                <tr>

                                    <td>

                                        <span class="dashboard-number">
                                            {{ $expense->category ?? '—' }}
                                        </span>

                                    </td>


                                    <td>

                                        {{ $expense->expense_date
                                            ? $expense->expense_date->format('M d, Y')
                                            : '—'
                                        }}

                                    </td>


                                    <td>

                                        {{ $expense->description
                                            ? \Illuminate\Support\Str::limit(
                                                $expense->description,
                                                28
                                            )
                                            : '—'
                                        }}

                                    </td>


                                    <td>

                                        <span class="dashboard-amount">

                                            ₱{{ number_format(
                                                (float) $expense->amount,
                                                2
                                            ) }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="dashboard-empty">

                    <div class="dashboard-empty-icon">
                        −
                    </div>

                    <div class="dashboard-empty-title">
                        No expenses recorded yet
                    </div>

                    <div class="dashboard-empty-text">
                        Business expenses will appear here.
                    </div>

                </div>

            @endif

        </div>

    </div>


</div>



{{-- ================================================================
     CHART.JS
     ================================================================ --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* ==========================================================
           DATABASE DATA
           ========================================================== */

        const salesActivity =
            {!! $salesActivityJson ?: '[]' !!};


        const paymentMethods =
            {!! $paymentMethodSummaryJson ?: '[]' !!};


        const expenseCategories =
            {!! $expenseCategorySummaryJson ?: '[]' !!};



        /* ==========================================================
           CHART DEFAULTS
           ========================================================== */

        Chart.defaults.font.family =
            "'Segoe UI', Arial, sans-serif";

        Chart.defaults.font.size = 10;

        Chart.defaults.color =
            '#8b8179';



        /* ==========================================================
           SALES ACTIVITY
           ========================================================== */

        const salesActivityCanvas =
            document.getElementById(
                'salesActivityChart'
            );


        if (salesActivityCanvas) {

            new Chart(
                salesActivityCanvas,
                {

                    type: 'line',

                    data: {

                        labels:
                            salesActivity.map(
                                item => item.date
                            ),

                        datasets: [

                            {

                                label: 'Sales',

                                data:
                                    salesActivity.map(
                                        item => Number(
                                            item.total ?? 0
                                        )
                                    ),

                                borderColor:
                                    '#c47a3a',

                                backgroundColor:
                                    'rgba(196,122,58,.10)',

                                borderWidth: 2,

                                fill: true,

                                tension: .35,

                                pointRadius: 2,

                                pointHoverRadius: 4

                            }

                        ]

                    },


                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        interaction: {

                            mode: 'index',

                            intersect: false

                        },


                        plugins: {

                            legend: {

                                position: 'top',

                                align: 'end',

                                labels: {

                                    usePointStyle: true,

                                    boxWidth: 7,

                                    padding: 15,

                                    font: {

                                        size: 10,

                                        weight: '600'

                                    }

                                }

                            },


                            tooltip: {

                                callbacks: {

                                    label:
                                        function(context) {

                                            return (
                                                ' Sales: ₱' +
                                                Number(
                                                    context.parsed.y
                                                ).toLocaleString(
                                                    'en-PH',
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                )
                                            );

                                        }

                                }

                            }

                        },


                        scales: {

                            x: {

                                grid: {

                                    display: false

                                },

                                ticks: {

                                    maxTicksLimit: 8,

                                    font: {

                                        size: 9

                                    }

                                }

                            },


                            y: {

                                beginAtZero: true,

                                grid: {

                                    color:
                                        '#eee8e2'

                                },

                                ticks: {

                                    font: {

                                        size: 9

                                    },

                                    callback:
                                        function(value) {

                                            return '₱' +
                                                Number(value)
                                                    .toLocaleString(
                                                        'en-PH',
                                                        {
                                                            notation:
                                                                'compact'
                                                        }
                                                    );

                                        }

                                }

                            }

                        }

                    }

                }

            );

        }



        /* ==========================================================
           PAYMENT METHODS
           ========================================================== */

        const paymentMethodCanvas =
            document.getElementById(
                'paymentMethodChart'
            );


        if (paymentMethodCanvas) {

            new Chart(
                paymentMethodCanvas,
                {

                    type: 'bar',

                    data: {

                        labels:
                            paymentMethods.map(
                                item =>
                                    item.payment_method
                                        ?? 'Unspecified'
                            ),

                        datasets: [

                            {

                                label: 'Sales',

                                data:
                                    paymentMethods.map(
                                        item => Number(
                                            item.total_amount
                                                ?? 0
                                        )
                                    ),

                                backgroundColor:
                                    '#c47a3a',

                                borderRadius: 6,

                                borderSkipped: false,

                                maxBarThickness: 28

                            }

                        ]

                    },


                    options: {

                        indexAxis: 'y',

                        responsive: true,

                        maintainAspectRatio: false,


                        plugins: {

                            legend: {

                                display: false

                            },


                            tooltip: {

                                callbacks: {

                                    label:
                                        function(context) {

                                            return (
                                                ' Sales: ₱' +
                                                Number(
                                                    context.parsed.x
                                                ).toLocaleString(
                                                    'en-PH',
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                )
                                            );

                                        }

                                }

                            }

                        },


                        scales: {

                            x: {

                                beginAtZero: true,

                                ticks: {

                                    font: {

                                        size: 9

                                    },

                                    callback:
                                        function(value) {

                                            return '₱' +
                                                Number(value)
                                                    .toLocaleString(
                                                        'en-PH',
                                                        {
                                                            notation:
                                                                'compact'
                                                        }
                                                    );

                                        }

                                },

                                grid: {

                                    color:
                                        '#eee8e2'

                                }

                            },


                            y: {

                                grid: {

                                    display: false

                                },

                                ticks: {

                                    font: {

                                        size: 9

                                    }

                                }

                            }

                        }

                    }

                }

            );

        }



        /* ==========================================================
           EXPENSE CATEGORIES
           ========================================================== */

        const expenseCategoryCanvas =
            document.getElementById(
                'expenseCategoryChart'
            );


        if (expenseCategoryCanvas) {

            const expenseLabels =
                expenseCategories.map(
                    item =>
                        item.category ?? 'Uncategorized'
                );


            const expenseValues =
                expenseCategories.map(
                    item =>
                        Number(
                            item.total_amount ?? 0
                        )
                );


            const totalExpenses =
                expenseValues.reduce(
                    function(total, value) {

                        return total + value;

                    },
                    0
                );


            new Chart(
                expenseCategoryCanvas,
                {

                    type: 'doughnut',

                    data: {

                        labels:
                            expenseLabels,

                        datasets: [

                            {

                                data:
                                    expenseValues,

                                backgroundColor: [

                                    '#c47a3a',

                                    '#5d8b67',

                                    '#637f9f',

                                    '#b9823e',

                                    '#b95d56',

                                    '#8b8179'

                                ],

                                borderColor:
                                    '#ffffff',

                                borderWidth: 3,

                                hoverOffset: 5

                            }

                        ]

                    },


                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        cutout: '68%',


                        plugins: {

                            legend: {

                                position: 'bottom',

                                labels: {

                                    usePointStyle: true,

                                    pointStyle: 'circle',

                                    padding: 16,

                                    color: '#756b63',

                                    font: {

                                        size: 10,

                                        weight: '600'

                                    }

                                }

                            },


                            tooltip: {

                                callbacks: {

                                    label:
                                        function(context) {

                                            const value =
                                                Number(
                                                    context.raw ?? 0
                                                );


                                            const percentage =
                                                totalExpenses > 0
                                                    ? (
                                                        value /
                                                        totalExpenses
                                                    ) * 100
                                                    : 0;


                                            return (
                                                ' ' +
                                                context.label +
                                                ': ₱' +
                                                value.toLocaleString(
                                                    'en-PH',
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                ) +
                                                ' (' +
                                                percentage.toFixed(1) +
                                                '%)'
                                            );

                                        }

                                }

                            }

                        }

                    },


                    plugins: [

                        {

                            id: 'expenseCategoryCenterText',

                            beforeDraw:
                                function(chart) {

                                    const width =
                                        chart.width;

                                    const height =
                                        chart.height;

                                    const ctx =
                                        chart.ctx;


                                    ctx.save();


                                    const centerX =
                                        width / 2;

                                    const centerY =
                                        height / 2;


                                    ctx.textAlign =
                                        'center';

                                    ctx.textBaseline =
                                        'middle';


                                    ctx.fillStyle =
                                        '#241a14';

                                    ctx.font =
                                        '800 20px Segoe UI, Arial, sans-serif';


                                    ctx.fillText(
                                        '₱' +
                                        totalExpenses.toLocaleString(
                                            'en-PH',
                                            {
                                                notation: 'compact'
                                            }
                                        ),
                                        centerX,
                                        centerY - 7
                                    );


                                    ctx.fillStyle =
                                        '#8b8179';

                                    ctx.font =
                                        '600 9px Segoe UI, Arial, sans-serif';


                                    ctx.fillText(
                                        'TOTAL EXPENSES',
                                        centerX,
                                        centerY + 14
                                    );


                                    ctx.restore();

                                }

                        }

                    ]

                }

            );

        }

    }

);

</script>

@endsection