@extends('layouts.app')

@section('title', 'BiteSync | Procurement')

@section('content')
<div class="procurement-page">
    <div class="topbar">
        <div class="page-title">
            <small>Procurement workspace</small>
            <h1>Good day, {{ $user->name }}</h1>
            <p>Create and track the supplier purchases assigned to you.</p>
        </div>
        <div class="date-box">{{ now()->format('F d, Y') }}</div>
    </div>

    <div class="purchase-stats">
        <div class="purchase-stat"><div><div class="purchase-stat-label">MY PURCHASES</div><div class="purchase-stat-value">{{ $stats['total'] }}</div><div class="purchase-stat-note">Records you created</div></div><div class="purchase-stat-icon">▦</div></div>
        <div class="purchase-stat"><div><div class="purchase-stat-label">DRAFTS</div><div class="purchase-stat-value">{{ $stats['draft'] }}</div><div class="purchase-stat-note">Still being prepared</div></div><div class="purchase-stat-icon">📝</div></div>
        <div class="purchase-stat"><div><div class="purchase-stat-label">PENDING</div><div class="purchase-stat-value">{{ $stats['pending'] }}</div><div class="purchase-stat-note">Awaiting approval</div></div><div class="purchase-stat-icon">⏳</div></div>
        <div class="purchase-stat"><div><div class="purchase-stat-label">RECEIVED</div><div class="purchase-stat-value">{{ $stats['received'] }}</div><div class="purchase-stat-note">Successfully received</div></div><div class="purchase-stat-icon">✓</div></div>
    </div>

    <div class="content-card procurement-card">
        <div class="card-header">
            <div><h2>Recent purchases</h2><p>Only purchases created from your procurement account.</p></div>
            <a class="button button-primary" href="{{ route('purchases.create') }}">+ New purchase</a>
        </div>
        @if ($recentPurchases->isEmpty())
            <div class="empty-state">No purchases yet. Create your first supplier order to get started.</div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Purchase no.</th><th>Supplier</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach ($recentPurchases as $purchase)
                        <tr>
                            <td><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->purchase_number }}</a></td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                            <td><span class="status-badge">{{ $purchase->status }}</span></td>
                            <td>₱{{ number_format((float) $purchase->total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.procurement-card { margin-top: 24px; }
.procurement-page .card-header { align-items: center; }
.procurement-page .card-header h2 { margin-bottom: 4px; }
.procurement-page .card-header p { color: var(--muted); }
.button { display: inline-block; padding: 10px 16px; border-radius: 9px; text-decoration: none; font-weight: 700; }
.button-primary { color: #fff; background: var(--orange); }
.button-primary:hover { background: var(--orange-dark); }
.empty-state { padding: 42px 16px; color: var(--muted); text-align: center; }
.data-table a { color: var(--orange-dark); font-weight: 700; text-decoration: none; }
.status-badge { padding: 5px 9px; border-radius: 999px; background: var(--orange-light); color: var(--orange-dark); font-size: .8rem; }
</style>
@endpush
