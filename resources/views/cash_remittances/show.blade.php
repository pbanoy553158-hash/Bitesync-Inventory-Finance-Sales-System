@extends('layouts.app')

@section('title', 'BiteSync | Cash Remittance')

@section('content')

@php
    $expected = (float) ($cashRemittance->expected_amount ?? 0);
    $actual = (float) ($cashRemittance->actual_amount ?? 0);
    $variance = $actual - $expected;

    if ($variance > 0) {
        $varianceClass = 'positive';
        $varianceLabel = 'Excess Cash';
        $variancePrefix = '+';
    } elseif ($variance < 0) {
        $varianceClass = 'negative';
        $varianceLabel = 'Cash Shortage';
        $variancePrefix = '−';
    } else {
        $varianceClass = 'balanced';
        $varianceLabel = 'Balanced';
        $variancePrefix = '';
    }

    $status = $cashRemittance->status ?? 'Recorded';

    $statusClass = match ($status) {
        'Verified' => 'verified',
        'Voided' => 'voided',
        default => 'recorded',
    };

    $statusIcon = match ($status) {
        'Voided' => '⊘',
        default => '✓',
    };

    $noticeText = match ($status) {
        'Verified' =>
            'This remittance has been verified by Finance and is included in the active remittance totals.',
        'Voided' =>
            'This remittance has been voided and is excluded from active remittance totals.',
        default =>
            'This remittance is currently recorded and remains available for Finance verification.',
    };
@endphp

<style>
    /* =========================================================
       CASH REMITTANCE SHOW
    ========================================================= */

    .cash-show-page {
        max-width: 1120px;
        margin: 0 auto;
        padding-bottom: 30px;
    }

    /* =========================================================
       TOPBAR
    ========================================================= */

    .cash-show-page .topbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 16px;
    }

    .cash-show-page .page-title small {
        display: block;
        margin-bottom: 5px;
        color: #a9825b;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .cash-show-page .page-title h1 {
        margin: 0;
        color: #27221e;
        font-size: 29px;
        line-height: 1.15;
        font-weight: 700;
    }

    .cash-show-page .page-title p {
        margin: 6px 0 0;
        color: #817870;
        font-size: 12px;
        line-height: 1.5;
    }

    .cash-show-page .date-box {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 145px;
        padding: 9px 12px;
        border: 1px solid #e6dfd8;
        border-radius: 10px;
        background: #fff;
        color: #6f665f;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .cash-show-page .date-icon {
        color: #a9825b;
        font-size: 15px;
        line-height: 1;
    }

    /* =========================================================
       BREADCRUMB
    ========================================================= */

    .cash-show-page .breadcrumb {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 17px;
        color: #8b8179;
        font-size: 10px;
    }

    .cash-show-page .breadcrumb a {
        color: #8b8179;
        text-decoration: none;
        transition: color .15s ease;
    }

    .cash-show-page .breadcrumb a:hover {
        color: #a96f38;
    }

    .cash-show-page .breadcrumb .current {
        color: #514942;
        font-weight: 600;
    }

    .cash-show-page .breadcrumb .separator {
        color: #b9aea5;
    }

    /* =========================================================
       INFORMATION GRID
    ========================================================= */

    .cash-information-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 17px;
    }

    .cash-information-panel {
        overflow: hidden;
        min-width: 0;
        border: 1px solid #e5ded7;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 3px 14px rgba(45, 35, 28, .035);
    }

    /* =========================================================
       PANEL HEADER
    ========================================================= */

    .cash-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 65px;
        padding: 14px 19px;
        border-bottom: 1px solid #eee8e2;
        background: #fff;
    }

    .cash-panel-heading {
        min-width: 0;
    }

    .cash-panel-heading small {
        display: block;
        margin-bottom: 4px;
        color: #a9825b;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .cash-panel-heading h2 {
        margin: 0;
        color: #2d2723;
        font-size: 17px;
        line-height: 1.2;
        font-weight: 700;
    }

    .cash-panel-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 31px;
        height: 31px;
        border-radius: 9px;
        background: #fbf3e9;
        color: #a96f38;
        font-size: 13px;
        font-weight: 700;
    }

    /* =========================================================
       PANEL BODY
    ========================================================= */

    .cash-panel-body {
        padding: 18px 19px 20px;
    }

    /* =========================================================
       REMITTANCE INFORMATION
    ========================================================= */

    .remittance-main-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 19px;
    }

    .detail-item {
        min-width: 0;
    }

    .detail-label {
        display: block;
        margin-bottom: 6px;
        color: #8a8078;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .detail-value {
        display: block;
        color: #302a26;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 600;
        word-break: break-word;
    }

    .reference-detail {
        padding-top: 17px;
        border-top: 1px solid #eee8e2;
    }

    .reference-value {
        color: #514840;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .02em;
    }

    .no-reference {
        color: #a09790;
        font-family: inherit;
        font-style: italic;
        font-weight: 400;
    }

    /* =========================================================
       STATUS
    ========================================================= */

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 23px;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-badge .status-icon {
        font-size: 10px;
        line-height: 1;
    }

    .status-badge.recorded {
        background: #eef7ef;
        color: #4d8058;
    }

    .status-badge.verified {
        background: #edf4fb;
        color: #4676a4;
    }

    .status-badge.voided {
        background: #fbeeee;
        color: #a85a5a;
    }

    /* =========================================================
       COLLECTION AMOUNTS
    ========================================================= */

    .amount-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 18px;
    }

    .amount-item {
        min-width: 0;
        padding: 12px 13px;
        border: 1px solid #eee8e2;
        border-radius: 10px;
        background: #fcfaf8;
    }

    .amount-item .detail-label {
        margin-bottom: 7px;
    }

    .amount-value {
        display: block;
        color: #2d2723;
        font-size: 17px;
        line-height: 1.2;
        font-weight: 700;
        white-space: nowrap;
    }

    .variance-detail {
        padding-top: 17px;
        border-top: 1px solid #eee8e2;
    }

    .variance-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .variance-main {
        min-width: 0;
    }

    .variance-value {
        display: block;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 700;
    }

    .variance-label {
        display: block;
        margin-top: 5px;
        font-size: 9px;
        font-weight: 600;
        line-height: 1.3;
    }

    .variance-mark {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        font-size: 14px;
        font-weight: 700;
    }

    .variance-positive .variance-value,
    .variance-positive .variance-label {
        color: #4d76a3;
    }

    .variance-positive .variance-mark {
        background: #edf4fb;
        color: #4d76a3;
    }

    .variance-negative .variance-value,
    .variance-negative .variance-label {
        color: #a95a5a;
    }

    .variance-negative .variance-mark {
        background: #fbeeee;
        color: #a95a5a;
    }

    .variance-balanced .variance-value,
    .variance-balanced .variance-label {
        color: #4f8258;
    }

    .variance-balanced .variance-mark {
        background: #eef7ef;
        color: #4f8258;
    }

    /* =========================================================
       REMARKS
    ========================================================= */

    .remarks-content {
        min-height: 76px;
        color: #554c45;
        font-size: 12px;
        line-height: 1.65;
        white-space: pre-line;
    }

    .remarks-empty {
        color: #a09790;
        font-style: italic;
    }

    /* =========================================================
       RECORD INFORMATION
    ========================================================= */

    .record-list {
        display: flex;
        flex-direction: column;
        gap: 13px;
    }

    .record-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        padding-bottom: 11px;
        border-bottom: 1px solid #f0ebe6;
    }

    .record-row:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .record-label {
        flex: 0 0 auto;
        color: #8a8078;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .record-value {
        color: #403934;
        font-size: 11px;
        line-height: 1.4;
        font-weight: 600;
        text-align: right;
        word-break: break-word;
    }

    .record-user {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .record-user-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 21px;
        height: 21px;
        border-radius: 50%;
        background: #f6eee5;
        color: #a96f38;
        font-size: 9px;
        font-weight: 700;
    }

    /* =========================================================
       INFORMATION NOTICE
    ========================================================= */

    .cash-notice {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin-bottom: 17px;
        padding: 13px 15px;
        border: 1px solid #eadfd3;
        border-left: 3px solid #b27c4b;
        border-radius: 10px;
        background: #fcf8f3;
    }

    .cash-notice-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #f3e5d7;
        color: #a96f38;
        font-size: 11px;
        font-weight: 700;
    }

    .cash-notice-content {
        min-width: 0;
    }

    .cash-notice-title {
        display: block;
        margin-bottom: 3px;
        color: #514840;
        font-size: 11px;
        font-weight: 700;
    }

    .cash-notice-text {
        margin: 0;
        color: #80756d;
        font-size: 10px;
        line-height: 1.5;
    }

    /* =========================================================
       ACTIONS
    ========================================================= */

    .cash-show-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
    }

    .cash-action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 37px;
        padding: 8px 14px;
        border: 1px solid #ddd4cc;
        border-radius: 8px;
        background: #fff;
        color: #554c45;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease,
            transform .15s ease;
    }

    .cash-action-button:hover {
        border-color: #cfc2b7;
        background: #faf7f4;
        color: #302a26;
    }

    .cash-action-button:active {
        transform: translateY(1px);
    }

    .cash-action-button.primary {
        border-color: #b77b3f;
        background: linear-gradient(135deg, #c98a4b, #a96d34);
        color: #fff;
        box-shadow: 0 3px 8px rgba(169, 109, 52, .16);
    }

    .cash-action-button.primary:hover {
        border-color: #9d642f;
        background: linear-gradient(135deg, #bd7c3f, #995f2d);
        color: #fff;
    }

    .cash-action-icon {
        font-size: 12px;
        line-height: 1;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 900px) {
        .cash-show-page {
            padding: 0 14px 28px;
        }

        .cash-show-page .topbar {
            align-items: flex-start;
        }
    }

    @media (max-width: 700px) {
        .cash-show-page .topbar {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .cash-show-page .date-box {
            width: fit-content;
        }

        .cash-information-grid {
            grid-template-columns: 1fr;
        }

        .remittance-main-details,
        .amount-details {
            grid-template-columns: 1fr 1fr;
        }

        .cash-show-actions {
            justify-content: stretch;
        }

        .cash-action-button {
            flex: 1;
        }
    }

    @media (max-width: 480px) {
        .cash-show-page .page-title h1 {
            font-size: 24px;
        }

        .cash-show-page .page-title p {
            font-size: 11px;
        }

        .cash-panel-header {
            padding: 13px 15px;
        }

        .cash-panel-body {
            padding: 16px 15px 18px;
        }

        .remittance-main-details,
        .amount-details {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .amount-item {
            padding: 11px 12px;
        }

        .cash-show-actions {
            flex-direction: column-reverse;
        }

        .cash-action-button {
            width: 100%;
        }
    }
</style>

<div class="cash-show-page">

    {{-- =====================================================
         TOPBAR
    ====================================================== --}}
    <div class="topbar">
        <div class="page-title">
            <small>Finance</small>

            <h1>Cash Remittance</h1>

            <p>
                Review the recorded cash collection and remittance details.
            </p>
        </div>

        <div class="date-box">
            <span class="date-icon">◷</span>
            {{ now()->format('F d, Y') }}
        </div>
    </div>

    {{-- =====================================================
         BREADCRUMB
    ====================================================== --}}
    <div class="breadcrumb">
        <a href="{{ route('cash-remittances.index') }}">
            Cash Remittances
        </a>

        <span class="separator">/</span>

        <span class="current">
            View Remittance
        </span>
    </div>

    {{-- =====================================================
         2 × 2 INFORMATION GRID
    ====================================================== --}}
    <div class="cash-information-grid">

        {{-- =================================================
             REMITTANCE INFORMATION
        ================================================== --}}
        <section class="cash-information-panel">

            <div class="cash-panel-header">
                <div class="cash-panel-heading">
                    <small>Transaction Details</small>
                    <h2>Remittance Information</h2>
                </div>

                <div class="cash-panel-icon">
                    ₱
                </div>
            </div>

            <div class="cash-panel-body">

                <div class="remittance-main-details">

                    <div class="detail-item">
                        <span class="detail-label">
                            Remittance Date
                        </span>

                        <span class="detail-value">
                            {{ $cashRemittance->remittance_date?->format('F d, Y') ?? '—' }}
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">
                            Status
                        </span>

                        <span class="status-badge {{ $statusClass }}">
                            <span class="status-icon">
                                {{ $statusIcon }}
                            </span>

                            {{ $status }}
                        </span>
                    </div>

                </div>

                <div class="reference-detail">
                    <span class="detail-label">
                        Reference No.
                    </span>

                    @if($cashRemittance->reference_no)
                        <span class="detail-value reference-value">
                            {{ $cashRemittance->reference_no }}
                        </span>
                    @else
                        <span class="detail-value reference-value no-reference">
                            No reference number
                        </span>
                    @endif
                </div>

            </div>
        </section>


        {{-- =================================================
             COLLECTION AMOUNTS
        ================================================== --}}
        <section class="cash-information-panel">

            <div class="cash-panel-header">
                <div class="cash-panel-heading">
                    <small>Financial Summary</small>
                    <h2>Collection Amounts</h2>
                </div>

                <div class="cash-panel-icon">
                    ₱
                </div>
            </div>

            <div class="cash-panel-body">

                <div class="amount-details">

                    <div class="amount-item">
                        <span class="detail-label">
                            Expected Amount
                        </span>

                        <span class="amount-value">
                            ₱{{ number_format($expected, 2) }}
                        </span>
                    </div>

                    <div class="amount-item">
                        <span class="detail-label">
                            Actual Amount
                        </span>

                        <span class="amount-value">
                            ₱{{ number_format($actual, 2) }}
                        </span>
                    </div>

                </div>

                <div class="variance-detail">

                    <span class="detail-label">
                        Variance
                    </span>

                    <div class="variance-row {{ 'variance-' . $varianceClass }}">

                        <div class="variance-main">

                            <span class="variance-value">
                                @if($variance == 0)
                                    ₱0.00
                                @else
                                    {{ $variancePrefix }}₱{{ number_format(abs($variance), 2) }}
                                @endif
                            </span>

                            <span class="variance-label">
                                {{ $varianceLabel }}
                            </span>

                        </div>

                        <div class="variance-mark">
                            @if($variance > 0)
                                +
                            @elseif($variance < 0)
                                −
                            @else
                                ✓
                            @endif
                        </div>

                    </div>

                </div>

            </div>
        </section>


        {{-- =================================================
             REMARKS
        ================================================== --}}
        <section class="cash-information-panel">

            <div class="cash-panel-header">
                <div class="cash-panel-heading">
                    <small>Additional Details</small>
                    <h2>Remarks</h2>
                </div>

                <div class="cash-panel-icon">
                    i
                </div>
            </div>

            <div class="cash-panel-body">

                <div class="remarks-content">
                    @if($cashRemittance->remarks)
                        {{ $cashRemittance->remarks }}
                    @else
                        <span class="remarks-empty">
                            No remarks were provided for this remittance.
                        </span>
                    @endif
                </div>

            </div>
        </section>


        {{-- =================================================
             RECORD INFORMATION
        ================================================== --}}
        <section class="cash-information-panel">

            <div class="cash-panel-header">
                <div class="cash-panel-heading">
                    <small>Audit Details</small>
                    <h2>Record Information</h2>
                </div>

                <div class="cash-panel-icon">
                    ✓
                </div>
            </div>

            <div class="cash-panel-body">

                <div class="record-list">

                    <div class="record-row">
                        <span class="record-label">
                            Recorded By
                        </span>

                        <span class="record-value record-user">
                            <span class="record-user-icon">
                                {{ strtoupper(substr($cashRemittance->user?->name ?? 'U', 0, 1)) }}
                            </span>

                            {{ $cashRemittance->user?->name ?? 'System User' }}
                        </span>
                    </div>

                    <div class="record-row">
                        <span class="record-label">
                            Created
                        </span>

                        <span class="record-value">
                            {{ $cashRemittance->created_at?->format('M d, Y h:i A') ?? '—' }}
                        </span>
                    </div>

                    <div class="record-row">
                        <span class="record-label">
                            Last Updated
                        </span>

                        <span class="record-value">
                            {{ $cashRemittance->updated_at?->format('M d, Y h:i A') ?? '—' }}
                        </span>
                    </div>

                </div>

            </div>
        </section>

    </div>


    {{-- =====================================================
         INFORMATION NOTICE
    ====================================================== --}}
    <div class="cash-notice">

        <div class="cash-notice-icon">
            i
        </div>

        <div class="cash-notice-content">

            <span class="cash-notice-title">
                Cash remittance record
            </span>

            <p class="cash-notice-text">
                {{ $noticeText }}
            </p>

        </div>

    </div>


    {{-- =====================================================
         ACTIONS
    ====================================================== --}}
    <div class="cash-show-actions">

        <a
            href="{{ route('cash-remittances.index') }}"
            class="cash-action-button"
        >
            <span class="cash-action-icon">←</span>
            Back to Cash Remittances
        </a>

        <a
            href="{{ route('cash-remittances.edit', $cashRemittance) }}"
            class="cash-action-button primary"
        >
            <span class="cash-action-icon">✎</span>
            Edit Remittance
        </a>

    </div>

</div>

@endsection