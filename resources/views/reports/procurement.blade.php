@extends('layouts.app')

@section('title', 'BiteSync | Procurement Report')

@section('content')
<div class="reports-page">
    <div class="topbar">
        <div class="page-title">
            <small>Reports</small>
            <h1>Procurement activity</h1>
            <p>This report includes only purchases created by {{ $user->name }}.</p>
        </div>
        <div class="date-box">{{ now()->format('F d, Y') }}</div>
    </div>

    <div class="purchase-stats">
        <div class="purchase-stat"><div><div class="purchase-stat-label">PURCHASES CREATED</div><div class="purchase-stat-value">{{ $summary['total'] }}</div></div><div class="purchase-stat-icon">▦</div></div>
        <div class="purchase-stat"><div><div class="purchase-stat-label">TOTAL VALUE</div><div class="purchase-stat-value">₱{{ number_format((float) $summary['value'], 2) }}</div></div><div class="purchase-stat-icon">₱</div></div>
        <div class="purchase-stat"><div><div class="purchase-stat-label">PENDING APPROVAL</div><div class="purchase-stat-value">{{ $summary['pending'] }}</div></div><div class="purchase-stat-icon">⏳</div></div>
        <div class="purchase-stat"><div><div class="purchase-stat-label">RECEIVED</div><div class="purchase-stat-value">{{ $summary['received'] }}</div></div><div class="purchase-stat-icon">✓</div></div>
    </div>

    <div class="content-card">
        <div class="card-header"><div><h2>My procurement activity</h2><p>Supplier orders recorded under your account.</p></div></div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Purchase no.</th><th>Supplier</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>
                @forelse ($purchases as $purchase)
                    <tr><td><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->purchase_number }}</a></td><td>{{ $purchase->supplier->name }}</td><td>{{ $purchase->purchase_date->format('M d, Y') }}</td><td>{{ $purchase->status }}</td><td>₱{{ number_format((float) $purchase->total, 2) }}</td></tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No procurement activity has been recorded.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($purchases->hasPages()) <div class="pagination-wrap">{{ $purchases->links() }}</div> @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.reports-page .content-card { margin-top: 24px; }
.reports-page .card-header h2 { margin-bottom: 4px; }
.reports-page .card-header p { color: var(--muted); }
.reports-page .data-table a { color: var(--orange-dark); font-weight: 700; text-decoration: none; }
.pagination-wrap { padding: 16px; }
</style>
@endpush
