@extends('layouts.app')

@section('title', 'BiteSync | Audit Logs')

@section('content')
<div class="audit-page">
    <header class="audit-heading">
        <div>
            <div class="audit-eyebrow">Administration</div>
            <h1>Audit Logs</h1>
            <p>Review authenticated changes made across the system.</p>
        </div>
    </header>

    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="audit-filters">
        <label class="audit-search">
            <span>Search</span>
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Action, user, or record">
        </label>
        <label>
            <span>From</span>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}">
        </label>
        <label>
            <span>To</span>
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}">
        </label>
        <button type="submit" class="audit-filter-button">Filter</button>
        @if ($filters)
            <a href="{{ route('admin.audit-logs.index') }}" class="audit-clear-button">Clear</a>
        @endif
    </form>

    <section class="audit-panel" aria-label="Recorded activity">
        <div class="audit-panel-header">
            <div>
                <h2>Recorded Activity</h2>
                <p>{{ number_format($auditLogs->total()) }} entries</p>
            </div>
        </div>

        <div class="audit-table-wrap">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th scope="col">When</th>
                        <th scope="col">Who</th>
                        <th scope="col">Action</th>
                        <th scope="col">Record</th>
                        <th scope="col">Result</th>
                        <th scope="col">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                        <tr>
                            <td data-label="When">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                            <td data-label="Who">
                                <span class="audit-user">{{ $log->user?->name ?? 'Deleted account' }}</span>
                                @if ($log->user?->email)
                                    <span class="audit-user-email">{{ $log->user->email }}</span>
                                @endif
                            </td>
                            <td data-label="Action">
                                <span class="audit-action">{{ $log->action }}</span>
                                @if ($log->route_name)
                                    <span class="audit-route">{{ $log->route_name }}</span>
                                @endif
                            </td>
                            <td data-label="Record">
                                @if ($log->subject_type)
                                    {{ $log->subject_type }}{{ $log->subject_id ? ' #' . $log->subject_id : '' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td data-label="Result">
                                <span class="audit-status {{ $log->status_code >= 400 ? 'is-failed' : 'is-complete' }}">
                                    {{ $log->status_code }}
                                </span>
                            </td>
                            <td data-label="IP Address">{{ $log->ip_address ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="audit-empty">No audit entries match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($auditLogs->hasPages())
            <div class="audit-pagination">{{ $auditLogs->links() }}</div>
        @endif
    </section>
</div>
@endsection

@push('styles')
<style>
.audit-page {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    color: #2c241f;
}

.audit-heading {
    margin-bottom: 20px;
}

.audit-eyebrow {
    color: #a9825b;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
}

.audit-heading h1 {
    margin: 5px 0;
    color: #241a14;
    font-size: 28px;
    line-height: 1.2;
}

.audit-heading p,
.audit-panel-header p {
    margin: 0;
    color: #81776f;
    font-size: 13px;
}

.audit-filters {
    display: flex;
    align-items: end;
    gap: 10px;
    margin-bottom: 15px;
    padding: 14px;
    border: 1px solid #e4dcd4;
    border-radius: 8px;
    background: #fff;
}

.audit-filters label {
    display: grid;
    gap: 5px;
    min-width: 145px;
    color: #625951;
    font-size: 11px;
    font-weight: 700;
}

.audit-filters .audit-search {
    flex: 1;
}

.audit-filters input {
    width: 100%;
    min-height: 38px;
    padding: 8px 10px;
    border: 1px solid #ddd4ca;
    border-radius: 6px;
    color: #34251d;
    font: inherit;
    font-weight: 400;
}

.audit-filter-button,
.audit-clear-button {
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    border: 1px solid #a85f28;
    border-radius: 6px;
    background: #a85f28;
    color: white;
    font: inherit;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.audit-clear-button {
    border-color: #ddd4ca;
    background: #fff;
    color: #625951;
}

.audit-panel {
    overflow: hidden;
    border: 1px solid #e4dcd4;
    border-radius: 8px;
    background: white;
}

.audit-panel-header {
    padding: 15px 17px;
    border-bottom: 1px solid #eee7e0;
}

.audit-panel-header h2 {
    margin: 0 0 3px;
    color: #34251d;
    font-size: 15px;
}

.audit-panel-header p {
    font-size: 11px;
}

.audit-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.audit-table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse;
    text-align: left;
}

.audit-table th,
.audit-table td {
    padding: 11px 13px;
    border-bottom: 1px solid #f0ebe6;
    vertical-align: top;
    font-size: 12px;
}

.audit-table th {
    background: #fbf9f6;
    color: #81776f;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    white-space: nowrap;
}

.audit-user,
.audit-user-email,
.audit-action,
.audit-route {
    display: block;
}

.audit-user,
.audit-action {
    color: #34251d;
    font-weight: 700;
}

.audit-user-email,
.audit-route {
    margin-top: 3px;
    color: #938980;
    font-size: 10px;
    overflow-wrap: anywhere;
}

.audit-status {
    display: inline-flex;
    min-width: 42px;
    justify-content: center;
    padding: 3px 7px;
    border-radius: 5px;
    background: #edf6ef;
    color: #356b41;
    font-size: 10px;
    font-weight: 800;
}

.audit-status.is-failed {
    background: #fff2f1;
    color: #a33e38;
}

.audit-empty {
    padding: 28px !important;
    color: #81776f;
    text-align: center;
}

.audit-pagination {
    padding: 13px 16px;
}

@media (max-width: 700px) {
    .audit-heading h1 {
        font-size: 24px;
    }

    .audit-filters {
        align-items: stretch;
        flex-direction: column;
    }

    .audit-filters label {
        min-width: 0;
    }

    .audit-filter-button,
    .audit-clear-button {
        width: 100%;
    }
}
</style>
@endpush
