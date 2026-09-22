@extends('layouts.app')

@section('title', 'BiteSync | Finance Dashboard')

@section('content')
<div class="finance-page">
    <div class="topbar">
        <div class="page-title">
            <small>Finance workspace</small>
            <h1>Good day, {{ $user->name }}</h1>
            <p>Monitor revenue, expenses, and financial performance in one place.</p>
        </div>
        <div class="date-box"><span class="date-icon">◔</span> {{ now()->format('F d, Y') }}</div>
    </div>

    <div class="purchase-stats">
        <div class="purchase-stat">
            <div>
                <div class="purchase-stat-label">TODAY'S SALES</div>
                <div class="purchase-stat-value">₱18.4K</div>
                <div class="purchase-stat-note">vs. yesterday</div>
            </div>
            <div class="purchase-stat-icon">₱</div>
        </div>

        <div class="purchase-stat">
            <div>
                <div class="purchase-stat-label">EXPENSES</div>
                <div class="purchase-stat-value">₱6.8K</div>
                <div class="purchase-stat-note">This week</div>
            </div>
            <div class="purchase-stat-icon">▣</div>
        </div>

        <div class="purchase-stat">
            <div>
                <div class="purchase-stat-label">NET PROFIT</div>
                <div class="purchase-stat-value">₱11.6K</div>
                <div class="purchase-stat-note">Current run rate</div>
            </div>
            <div class="purchase-stat-icon">↗</div>
        </div>

        <div class="purchase-stat">
            <div>
                <div class="purchase-stat-label">PENDING PAYMENTS</div>
                <div class="purchase-stat-value">14</div>
                <div class="purchase-stat-note">Awaiting settlement</div>
            </div>
            <div class="purchase-stat-icon">⏳</div>
        </div>
    </div>

    <div class="content-card" style="margin-top: 24px;">
        <div class="card-header">
            <div>
                <h2 style="margin-bottom: 4px;">Financial summary</h2>
                <p style="color: var(--muted);">Role: {{ $user->role }}</p>
            </div>
            <a class="button button-primary" href="#">View reports</a>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Current</th>
                        <th>Target</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sales</td>
                        <td>₱18,400</td>
                        <td>₱20,000</td>
                        <td><span class="status-badge">On track</span></td>
                    </tr>
                    <tr>
                        <td>Operational cost</td>
                        <td>₱6,800</td>
                        <td>₱7,500</td>
                        <td><span class="status-badge status-good">Healthy</span></td>
                    </tr>
                    <tr>
                        <td>Receivables</td>
                        <td>₱3,250</td>
                        <td>₱4,000</td>
                        <td><span class="status-badge">Moderate</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.finance-page .card-header { align-items: center; }
.button { display: inline-block; padding: 10px 16px; border-radius: 9px; text-decoration: none; font-weight: 700; }
.button-primary { color: #fff; background: var(--orange); }
.button-primary:hover { background: var(--orange-dark); }
.status-badge { display: inline-block; padding: 5px 9px; border-radius: 999px; background: var(--orange-light); color: var(--orange-dark); font-size: .8rem; }
.status-good { background: var(--green-light); color: var(--green); }
</style>
@endpush