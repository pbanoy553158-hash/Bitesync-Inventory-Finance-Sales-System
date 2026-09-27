@extends('layouts.app')

@section('title', 'BiteSync | Expenses')

@section('content')

@php
    $canManageExpenses = auth()->user()?->role === 'Finance' || auth()->user()?->role === 'CEO/Admin';
    $categories = $categories ?? [];
    $months = collect();
    $statusClasses = [
        'Recorded' => 'expense-status-recorded',
        'Draft' => 'expense-status-draft',
        'Cancelled' => 'expense-status-cancelled',
    ];
@endphp

<div class="expense-page">

    <div class="topbar">
        <div class="page-title">
            <small>Expense Management</small>
            <h1>Expenses</h1>
            <p>Track operating expenses, references, and recorded costs.</p>
        </div>

        <div class="date-box">
            <span class="date-icon">◷</span>
            {{ now()->format('F d, Y') }}
        </div>
    </div>

    <section class="expense-stats">
        <div class="expense-stat">
            <div class="expense-stat-left">
                <div class="expense-stat-label">TOTAL EXPENSES</div>
                <div class="expense-stat-note">All recorded expenses</div>
            </div>
            <div class="expense-stat-right">
                <div class="expense-stat-icon">▦</div>
                <div class="expense-stat-value">{{ number_format((float) ($totalExpenses ?? 0), 2) }}</div>
            </div>
        </div>

        <div class="expense-stat">
            <div class="expense-stat-left">
                <div class="expense-stat-label">RECORDS</div>
                <div class="expense-stat-note">Total expense entries</div>
            </div>
            <div class="expense-stat-right">
                <div class="expense-stat-icon">✓</div>
                <div class="expense-stat-value">{{ number_format((int) ($expenseCount ?? 0)) }}</div>
            </div>
        </div>

        <div class="expense-stat">
            <div class="expense-stat-left">
                <div class="expense-stat-label">THIS MONTH</div>
                <div class="expense-stat-note">Current month total</div>
            </div>
            <div class="expense-stat-right">
                <div class="expense-stat-icon">◷</div>
                <div class="expense-stat-value">{{ number_format((float) ($monthlyExpenses ?? 0), 2) }}</div>
            </div>
        </div>

        <div class="expense-stat">
            <div class="expense-stat-left">
                <div class="expense-stat-label">AVERAGE</div>
                <div class="expense-stat-note">Average expense amount</div>
            </div>
            <div class="expense-stat-right">
                <div class="expense-stat-icon">₱</div>
                <div class="expense-stat-value expense-money">₱{{ number_format((float) ($averageExpense ?? 0), 2) }}</div>
            </div>
        </div>
    </section>

    <div class="expense-panel">
        <div class="expense-panel-header">
            <div>
                <div class="expense-panel-title">Expense Records</div>
                <div class="expense-panel-subtitle">Search and manage your expense transactions.</div>
            </div>

            @if ($canManageExpenses)
                <a href="{{ route('expenses.create') }}" class="expense-add-button">
                    <span>+</span>
                    New Expense
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('expenses.index') }}" class="expense-filters">
            <div class="expense-search-wrapper">
                <span class="expense-search-icon">⌕</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category, description, or reference...">
            </div>

            <select name="category">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>

            <input type="month" name="month" value="{{ request('month') }}" aria-label="Filter by month">

            <button type="submit" class="expense-filter-button">Filter</button>

            @if (request('search') || request('category') || request('month'))
                <a href="{{ route('expenses.index') }}" class="expense-clear-button">Clear</a>
            @endif
        </form>

        @if (session('success'))
            <div class="expense-alert expense-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="expense-alert expense-alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="expense-table-wrapper">
            <table class="expense-table">
                <thead>
                    <tr>
                        <th>Expense ID</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Recorded By</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="expense-actions-header">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($expenses as $expense)
                        @php
                            $status = $expense->status ?? 'Recorded';
                            $statusClass = $statusClasses[$status] ?? 'expense-status-recorded';
                        @endphp

                        <tr>
                            <td>
                                <a href="{{ route('expenses.show', $expense) }}" class="expense-number">
                                    {{ $expense->expense_no ?? sprintf('EXP-%06d', $expense->id) }}
                                </a>
                            </td>
                            <td><span class="expense-category">{{ $expense->category }}</span></td>
                            <td>{{ $expense->expense_date ? $expense->expense_date->format('M d, Y') : '—' }}</td>
                            <td>{{ $expense->recorded_by ?? ($expense->user?->name ?? '—') }}</td>
                            <td>{{ $expense->reference_no ?: '—' }}</td>
                            <td class="expense-amount">₱{{ number_format((float) $expense->amount, 2) }}</td>
                            <td><span class="expense-status {{ $statusClass }}">{{ $status }}</span></td>
                            <td class="expense-action-cell">
                                <div class="expense-inline-actions">
                                    <a href="{{ route('expenses.edit', $expense) }}">Edit</a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="expense-delete-button">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="expense-empty-state">
                                    <div class="expense-empty-icon">₱</div>
                                    <h3>No expenses recorded yet.</h3>
                                    <p>Start by adding your first expense entry.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="expense-pagination">
        {{ $expenses->appends(request()->query())->links() }}
    </div>
</div>

<style>
    .expense-page {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 10px 28px;
    }

    .topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 18px;
        padding: 10px 0 0;
    }

    .page-title small {
        display: block;
        margin-bottom: 4px;
        color: #a9825b;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .page-title h1 {
        margin: 0;
        color: var(--dark);
        font-size: clamp(1.6rem, 2vw, 1.9rem);
        line-height: 1.15;
        letter-spacing: -0.04rem;
        font-weight: 800;
    }

    .page-title p {
        margin-top: 6px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .date-box {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-width: 150px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: white;
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 3px 12px rgba(43, 31, 23, .025);
    }

    .date-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--orange-light);
        color: var(--orange);
        font-size: 16px;
    }

    .expense-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 17px;
    }

    .expense-stat {
        min-height: 125px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        padding: 16px 17px 15px;
        border: 1px solid var(--border);
        border-radius: 15px;
        background: white;
        box-shadow: 0 5px 18px rgba(43, 31, 23, 0.045);
        position: relative;
        overflow: hidden;
    }

    .expense-stat::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--orange), #e2a16c);
    }

    .expense-stat-left {
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

    .expense-stat-label {
        color: var(--muted);
        font-size: 0.6875rem;
        line-height: 1.3;
        font-weight: 800;
        letter-spacing: 0.045rem;
        white-space: nowrap;
    }

    .expense-stat-note {
        margin-top: 58px;
        color: #9d958f;
        font-size: 0.6875rem;
        line-height: 1.4;
        max-width: 155px;
    }

    .expense-stat-right {
        min-width: 82px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-start;
        padding-top: 3px;
        flex-shrink: 0;
    }

    .expense-stat-icon {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: linear-gradient(135deg, #fbf1e7, #f4e3d4);
        color: var(--orange);
        font-size: 0.8125rem;
        font-weight: 800;
    }

    .expense-stat-value {
        margin-top: 13px;
        color: var(--dark);
        font-size: clamp(1.45rem, 1.8vw, 1.75rem);
        line-height: 1;
        font-weight: 800;
        text-align: right;
        white-space: nowrap;
    }

    .expense-money {
        font-size: clamp(1rem, 1.3vw, 1.25rem);
        letter-spacing: -0.02rem;
    }

    .expense-panel {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 15px;
        background: rgba(255,255,255,0.7);
        box-shadow: 0 4px 18px rgba(43, 31, 23, .03);
    }

    .expense-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        min-height: 68px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--border);
        background: rgba(255,255,255,0.22);
    }

    .expense-panel-title {
        color: var(--dark);
        font-size: 1.05rem;
        line-height: 1.3;
        font-weight: 800;
    }

    .expense-panel-subtitle {
        margin-top: 3px;
        font-size: 11px;
        color: var(--muted);
    }

    .expense-add-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--orange), var(--orange-dark));
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
        box-shadow: 0 3px 9px rgba(168,95,40,.18);
    }

    .expense-add-button span {
        font-size: 15px;
        line-height: 1;
    }

    .expense-filters {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--border);
        background: var(--card-soft);
    }

    .expense-search-wrapper {
        position: relative;
        flex: 1;
        min-width: 180px;
    }

    .expense-search-wrapper input {
        width: 100%;
        height: 36px;
        padding: 0 12px 0 34px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: white;
        color: var(--text);
        font-family: inherit;
        font-size: 12px;
        outline: none;
    }

    .expense-search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 14px;
    }

    .expense-filters select,
    .expense-filters input[type="month"] {
        height: 36px;
        padding: 0 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: white;
        color: var(--text);
        font-family: inherit;
        font-size: 12px;
    }

    .expense-filter-button,
    .expense-clear-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .expense-filter-button {
        background: #2f241d;
        color: white;
    }

    .expense-clear-button {
        background: white;
        border-color: var(--border);
        color: var(--text);
    }

    .expense-alert {
        margin: 14px 18px 0;
        padding: 11px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .expense-alert-success {
        border: 1px solid rgba(93,139,103,.25);
        background: var(--green-light);
        color: var(--green);
    }

    .expense-alert-error {
        border: 1px solid rgba(185,93,86,.2);
        background: var(--red-light);
        color: var(--red);
    }

    .expense-table-wrapper {
        overflow-x: auto;
    }

    .expense-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 960px;
    }

    .expense-table thead th {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        background: rgba(255,255,255,.36);
        color: #665a51;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        text-align: left;
    }

    .expense-table tbody td {
        padding: 15px 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        font-size: 13px;
        vertical-align: middle;
    }

    .expense-table tbody tr:last-child td {
        border-bottom: none;
    }

    .expense-number {
        color: var(--dark);
        text-decoration: none;
        font-weight: 700;
    }

    .expense-category {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 999px;
        background: #f4e8dc;
        color: #7d5638;
        font-size: 11px;
        font-weight: 700;
    }

    .expense-amount {
        font-weight: 800;
        color: var(--dark);
    }

    .expense-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .expense-status-recorded {
        background: var(--green-light);
        color: var(--green);
    }

    .expense-status-draft {
        background: var(--yellow-light);
        color: var(--yellow);
    }

    .expense-status-cancelled {
        background: var(--red-light);
        color: var(--red);
    }

    .expense-inline-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .expense-inline-actions a,
    .expense-delete-button {
        color: var(--dark);
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        background: none;
        border: none;
        cursor: pointer;
    }

    .expense-delete-button {
        color: var(--red);
    }

    .expense-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 48px 16px 38px;
        color: var(--muted);
    }

    .expense-empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        margin-bottom: 12px;
        border-radius: 12px;
        background: var(--orange-light);
        color: var(--orange);
        font-size: 22px;
        font-weight: 800;
    }

    .expense-empty-state h3 {
        margin: 0;
        color: var(--dark);
        font-size: 1.15rem;
        font-weight: 800;
    }

    .expense-empty-state p {
        margin-top: 6px;
        font-size: 13px;
    }

    .expense-pagination {
        display: flex;
        justify-content: flex-end;
        padding: 16px 12px 10px;
    }

    .expense-pagination nav {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .expense-pagination .page-link,
    .expense-pagination .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        padding: 0 8px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: white;
        color: var(--text);
        text-decoration: none;
        font-size: 12px;
    }

    .expense-pagination .page-item.active .page-link {
        background: #2f241d;
        border-color: #2f241d;
        color: white;
    }

    @media (max-width: 980px) {
        .expense-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .topbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .expense-stats {
            grid-template-columns: 1fr;
        }

        .expense-panel-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .expense-filters {
            flex-wrap: wrap;
        }

        .expense-search-wrapper {
            width: 100%;
        }
    }
</style>

@endsection
