@extends('layouts.app')

@section('title', 'BiteSync | Products')

@section('content')

<div class="products-page">

<!-- =========================================================
     PRODUCTS TOPBAR
========================================================== -->

<div class="topbar">

    <div class="page-title">

        <small>
            Product Management
        </small>

        <h1>
            Products
        </h1>

        <p>
            Manage your BiteSync menu and product records.
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
========================================================== -->

<section class="products-stats">

    <!-- TOTAL PRODUCTS -->

    <div class="products-stat">

        <div class="products-stat-left">

            <div class="products-stat-label">
                TOTAL PRODUCTS
            </div>

            <div class="products-stat-note">
                All product records
            </div>

        </div>


        <div class="products-stat-right">

            <div class="products-stat-icon">
                ▦
            </div>

            <div class="products-stat-value">
                {{ $totalProducts }}
            </div>

        </div>

    </div>


    <!-- ACTIVE PRODUCTS -->

    <div class="products-stat">

        <div class="products-stat-left">

            <div class="products-stat-label">
                ACTIVE PRODUCTS
            </div>

            <div class="products-stat-note">
                Currently available
            </div>

        </div>


        <div class="products-stat-right">

            <div class="products-stat-icon">
                ✓
            </div>

            <div class="products-stat-value">
                {{ $activeProducts }}
            </div>

        </div>

    </div>


    <!-- INACTIVE PRODUCTS -->

    <div class="products-stat">

        <div class="products-stat-left">

            <div class="products-stat-label">
                INACTIVE PRODUCTS
            </div>

            <div class="products-stat-note">
                Not currently available
            </div>

        </div>


        <div class="products-stat-right">

            <div class="products-stat-icon">
                ×
            </div>

            <div class="products-stat-value">
                {{ $inactiveProducts }}
            </div>

        </div>

    </div>


    <!-- ACTIVE SELLING VALUE -->

    <div class="products-stat">

        <div class="products-stat-left">

            <div class="products-stat-label">
                ACTIVE SELLING VALUE
            </div>

            <div class="products-stat-note">
                Sum of active selling prices
            </div>

        </div>


        <div class="products-stat-right">

            <div class="products-stat-icon">
                {{ $currencySymbol }}
            </div>

            <div class="products-stat-value products-money">
                {{ $currencySymbol }}{{ number_format((float) $totalProductValue, 2) }}
            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     PRODUCT RECORDS PANEL
========================================================== -->

<div class="products-panel">

    <!-- =====================================================
         PANEL HEADER
    ====================================================== -->

    <div class="products-panel-header">

        <div>

            <div class="products-panel-title">
                Product Records
            </div>

            <div class="products-panel-subtitle">
                Search and manage your café product records.
            </div>

        </div>


        @if ($user->role === 'CEO/Admin')

            <a
                href="{{ route('products.create') }}"
                class="products-add-button"
            >

                <span>+</span>

                Add Product

            </a>

        @endif

    </div>


    <!-- =====================================================
         SEARCH / FILTER
    ====================================================== -->

    <form
        method="GET"
        action="{{ route('products.index') }}"
        class="products-filters"
    >

        <div class="products-search-wrapper">

            <span class="products-search-icon">
                ⌕
            </span>

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search products..."
            >

        </div>


        <select name="status">

            <option value="">
                All Status
            </option>

            <option
                value="active"
                {{ $status === 'active' ? 'selected' : '' }}
            >
                Active
            </option>

            <option
                value="inactive"
                {{ $status === 'inactive' ? 'selected' : '' }}
            >
                Inactive
            </option>

        </select>


        <button
            type="submit"
            class="products-filter-button"
        >
            Filter
        </button>


        @if ($search || $status)

            <a
                href="{{ route('products.index') }}"
                class="products-clear-button"
            >
                Clear
            </a>

        @endif

    </form>


    <!-- =====================================================
         PRODUCT TABLE
    ====================================================== -->

    <div class="products-table-wrapper">

        <table class="products-table">

            <thead>

                <tr>

                    <th>
                        Product
                    </th>

                    <th>
                        SKU
                    </th>

                    <th>
                        Category
                    </th>

                    <th>
                        Selling Price
                    </th>

                    <th>
                        Status
                    </th>

                    @if ($user->role === 'CEO/Admin')

                        <th class="products-actions-header">
                            Actions
                        </th>

                    @endif

                </tr>

            </thead>


            <tbody>

                @forelse ($products as $product)

                    <tr>

                        <!-- PRODUCT -->

                        <td>

                            <div class="products-item-cell">

                                <div class="products-item-image">

                                    @if ($product->image)

                                        <img
                                            src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                        >

                                    @else

                                        <span class="products-fallback-icon">
                                            🍔
                                        </span>

                                    @endif

                                </div>


                                <div class="products-item-info">

                                    <div class="products-name">
                                        {{ $product->name }}
                                    </div>


                                    @if ($product->description)

                                        <div class="products-description">
                                            {{ $product->description }}
                                        </div>

                                    @endif

                                </div>

                            </div>

                        </td>


                        <!-- SKU -->

                        <td>

                            <span class="products-sku">
                                {{ $product->sku }}
                            </span>

                        </td>


                        <!-- CATEGORY -->

                        <td>

                            @if ($product->category)

                                <span class="products-category">
                                    {{ $product->category->name }}
                                </span>

                            @else

                                <span class="products-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <!-- SELLING PRICE -->

                        <td>

                            <span class="products-price">
                                {{ $currencySymbol }}{{ number_format((float) $product->selling_price, 2) }}
                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>

                            @if ($product->is_active)

                                <span class="products-status products-status-active">
                                    ACTIVE
                                </span>

                            @else

                                <span class="products-status products-status-inactive">
                                    INACTIVE
                                </span>

                            @endif

                        </td>


                        <!-- ACTIONS -->

                        @if ($user->role === 'CEO/Admin')

                            <td>

                                <div class="products-table-actions">

                                    <a
                                        href="{{ route('recipes.edit', $product) }}"
                                        class="products-recipe-button"
                                    >

                                        <span>
                                            ≡
                                        </span>

                                        Recipe

                                    </a>


                                    <a
                                        href="{{ route('products.edit', $product) }}"
                                        class="products-edit-button"
                                    >

                                        <span>
                                            ✎
                                        </span>

                                        Edit

                                    </a>

                                </div>

                            </td>

                        @endif

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="{{ $user->role === 'CEO/Admin' ? 6 : 5 }}"
                            class="products-empty-state"
                        >

                            <div class="products-empty-icon">
                                ▦
                            </div>


                            <div class="products-empty-title">
                                No product records found
                            </div>


                            <div class="products-empty-description">

                                @if ($search || $status)

                                    Try changing your search or filter.

                                @else

                                    Your product records will appear here.

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

    @if ($products->hasPages())

        <div class="products-pagination-wrapper">

            <div class="products-pagination-inner">

                <div class="products-pagination-info">

                    Showing
                    <strong>{{ $products->firstItem() }}</strong>
                    to
                    <strong>{{ $products->lastItem() }}</strong>
                    of
                    <strong>{{ $products->total() }}</strong>
                    records

                </div>


                <div class="products-pagination-links">

                    {{ $products->links() }}

                </div>

            </div>

        </div>

    @endif

</div>

</div>

@endsection

@push('styles')

<style>

/* =========================================================
   PRODUCTS PAGE
========================================================= */

.products-page {
    width: 100%;
}


/* =========================================================
   PAGE TITLE
========================================================= */

.products-page .page-title h1 {

    font-size:
        clamp(1.6rem, 2vw, 1.9rem);

    line-height: 1.15;

    letter-spacing: -0.04rem;
}


/* =========================================================
   SUMMARY CARDS
========================================================= */

.products-stats {

    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 17px;

    margin-bottom: 17px;
}


.products-stat {

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

.products-stat::before {

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

.products-stat-left {

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
   HEADER LABEL
========================================================= */

.products-stat-label {

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1.3;

    font-weight: 800;

    letter-spacing: 0.045rem;

    white-space: nowrap;
}


/* =========================================================
   SUBTEXT
========================================================= */

.products-stat-note {

    margin-top: 58px;

    color: #9d958f;

    font-size: 0.6875rem;

    line-height: 1.4;

    max-width: 155px;
}


/* =========================================================
   RIGHT SIDE
========================================================= */

.products-stat-right {

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

.products-stat-icon {

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

.products-stat-value {

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
   MONEY VALUE
========================================================= */

.products-money {

    font-size:
        clamp(1rem, 1.3vw, 1.25rem);

    letter-spacing: -0.02rem;
}


/* =========================================================
   PRODUCTS PANEL
========================================================= */

.products-panel {

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

.products-panel-header {

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


.products-panel-title {

    color: var(--dark);

    font-size: 0.875rem;

    line-height: 1.3;

    font-weight: 700;
}


.products-panel-subtitle {

    margin-top: 3px;

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1.4;
}


/* =========================================================
   ADD PRODUCT
========================================================= */

.products-add-button {

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


.products-add-button:hover {

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


.products-add-button span {

    font-size: 0.8125rem;

    line-height: 1;
}


/* =========================================================
   FILTERS
========================================================= */

.products-filters {

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


.products-search-wrapper {

    flex: 1;

    position: relative;

    min-width: 180px;
}


.products-search-wrapper input {

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


.products-search-wrapper input:focus {

    border-color: #d5a77d;

    box-shadow:
        0 0 0 3px
        rgba(196, 122, 58, 0.07);
}


.products-search-wrapper input::placeholder {

    color: #aaa19a;
}


.products-search-icon {

    position: absolute;

    left: 11px;

    top: 50%;

    transform:
        translateY(-50%);

    color: var(--muted);

    font-size: 0.875rem;

    pointer-events: none;
}


.products-filters select {

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
   FILTER BUTTON
========================================================= */

.products-filter-button,
.products-clear-button {

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
}


.products-filter-button {

    border: none;

    background: var(--dark);

    color: white;

    transition:
        background-color 0.18s ease;
}


.products-filter-button:hover {

    background: var(--dark-soft);
}


.products-clear-button {

    border:
        1px solid var(--border);

    background: white;

    color: var(--muted);

    transition:
        background-color 0.18s ease,
        color 0.18s ease;
}


.products-clear-button:hover {

    background: #faf7f3;

    color: var(--dark);
}


/* =========================================================
   TABLE
========================================================= */

.products-table-wrapper {

    width: 100%;

    overflow-x: auto;
}


.products-table {

    width: 100%;

    min-width: 900px;

    border-collapse: collapse;
}


.products-table th {

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


.products-table td {

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


.products-table tbody tr {

    background: white;
}


.products-table tbody tr:hover {

    background: #fdfaf7;
}


/* =========================================================
   PRODUCT ITEM
========================================================= */

.products-item-cell {

    display: flex;

    align-items: center;

    gap: 10px;
}


.products-item-image {

    width: 38px;

    height: 38px;

    border-radius: 50%;

    overflow: hidden;

    flex-shrink: 0;

    background: #f8f1e9;

    display: flex;

    align-items: center;

    justify-content: center;

    border:
        1px solid #f0e6db;
}


.products-item-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;
}


.products-fallback-icon {

    font-size: 1.05rem;

    line-height: 1;
}


.products-item-info {

    min-width: 0;
}


.products-name {

    color: var(--dark);

    font-size: 0.75rem;

    line-height: 1.4;

    font-weight: 700;
}


.products-description {

    max-width: 210px;

    margin-top: 2px;

    color: var(--muted);

    font-size: 0.625rem;

    line-height: 1.35;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* =========================================================
   SKU
========================================================= */

.products-sku {

    display: inline-block;

    padding:
        3px
        5px;

    border-radius: 5px;

    background: #f5f0eb;

    color: var(--brown);

    font-family: monospace;

    font-size: 0.625rem;
}


/* =========================================================
   CATEGORY
========================================================= */

.products-category {

    display: inline-flex;

    align-items: center;

    padding:
        4px
        6px;

    border-radius: 6px;

    background: #f6f3f0;

    color: #75675d;

    font-size: 0.625rem;

    line-height: 1.2;

    font-weight: 600;
}


.products-muted {

    color: #a39b95;

    font-size: 0.75rem;
}


/* =========================================================
   PRICE
========================================================= */

.products-price {

    color: var(--dark);

    font-size: 0.75rem;

    font-weight: 800;

    white-space: nowrap;
}


/* =========================================================
   STATUS
========================================================= */

.products-status {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 4px;

    padding:
        4px
        7px;

    border-radius: 7px;

    font-size: 0.625rem;

    line-height: 1.2;

    font-weight: 800;

    white-space: nowrap;

    letter-spacing: 0.015rem;
}


.products-status::before {

    content: "";

    width: 5px;

    height: 5px;

    flex-shrink: 0;

    border-radius: 50%;

    background: currentColor;
}


.products-status-active {

    color: var(--green);

    background: var(--green-light);
}


.products-status-inactive {

    color: var(--muted);

    background: #f1eeeb;
}


/* =========================================================
   ACTIONS
========================================================= */

.products-actions-header {

    text-align: center !important;
}


.products-table-actions {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 4px;

    white-space: nowrap;
}


/* =========================================================
   RECIPE BUTTON
========================================================= */

.products-recipe-button {

    height: 27px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 3px;

    padding:
        0
        7px;

    border:
        1px solid #d7e5dc;

    border-radius: 7px;

    background: #f6fbf8;

    color: var(--green);

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


.products-recipe-button:hover {

    background: #edf8f1;

    border-color: #c4dccd;

    color: #2e6c46;
}


.products-recipe-button span {

    font-size: 0.6875rem;

    line-height: 1;
}


/* =========================================================
   EDIT BUTTON
========================================================= */

.products-edit-button {

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


.products-edit-button:hover {

    background: #faf5ef;

    border-color: #cdb49f;

    color: #68482f;
}


.products-edit-button span {

    font-size: 0.6875rem;

    line-height: 1;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.products-empty-state {

    padding:
        55px
        20px !important;

    text-align: center !important;
}


.products-empty-icon {

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


.products-empty-title {

    color: var(--dark);

    font-size: 0.8125rem;

    font-weight: 700;
}


.products-empty-description {

    margin-top: 4px;

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1.4;
}


/* =========================================================
   PAGINATION
========================================================= */

.products-pagination-wrapper {

    padding:
        12px
        18px;

    border-top:
        1px solid var(--border);

    background: white;
}


.products-pagination-inner {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;
}


.products-pagination-info {

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1.4;

    white-space: nowrap;
}


.products-pagination-info strong {

    color: var(--dark);

    font-weight: 700;
}


.products-pagination-links {

    display: flex;

    align-items: center;

    justify-content: flex-end;
}


.products-pagination-links nav {

    display: flex;

    align-items: center;

    justify-content: center;
}


.products-pagination-links nav > div {

    display: flex;

    align-items: center;

    justify-content: center;
}


.products-pagination-links nav > div > span,
.products-pagination-links nav > div > a {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-width: 30px;

    height: 30px;

    margin-left: 4px;

    padding:
        0
        8px;

    border:
        1px solid var(--border);

    border-radius: 7px;

    background: white;

    color: var(--muted);

    font-size: 0.6875rem;

    line-height: 1;

    font-weight: 700;

    text-decoration: none;

    transition:
        background-color 0.18s ease,
        border-color 0.18s ease,
        color 0.18s ease;
}


.products-pagination-links nav > div > a:hover {

    background: #faf5ef;

    border-color: #d8c2ae;

    color: var(--orange);
}


/* =========================================================
   ACTIVE PAGE
========================================================= */

.products-pagination-links nav > div > span[aria-current="page"] {

    border-color: var(--orange);

    background:
        linear-gradient(
            135deg,
            var(--orange),
            var(--orange-dark)
        );

    color: white;

    box-shadow:
        0 3px 8px
        rgba(168, 95, 40, 0.12);
}


/* =========================================================
   DISABLED BUTTONS
========================================================= */

.products-pagination-links nav > div > span[aria-disabled="true"] {

    opacity: 0.45;

    cursor: not-allowed;

    background: #faf8f6;

    color: #aaa19a;
}


/* =========================================================
   PAGINATION SVG
========================================================= */

.products-pagination-links svg {

    width: 14px;

    height: 14px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {

    .products-stats {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


@media (max-width: 700px) {

    .products-stats {

        grid-template-columns: 1fr;
    }


    .products-stat {

        min-height: 115px;
    }


    .products-stat-left {

        padding-top: 1px;
    }


    .products-stat-right {

        min-width: 72px;

        padding-top: 3px;
    }


    .products-stat-value {

        font-size: 1.45rem;
    }


    .products-money {

        font-size: 1.05rem;
    }


    .products-stat-note {

        margin-top: 55px;
    }


    .products-panel-header {

        align-items: flex-start;

        flex-direction: column;
    }


    .products-add-button {

        width: 100%;
    }


    .products-filters {

        align-items: stretch;

        flex-direction: column;
    }


    .products-search-wrapper {

        width: 100%;
    }


    .products-filters select,
    .products-filter-button,
    .products-clear-button {

        width: 100%;
    }


    .products-table-actions {

        justify-content: flex-start;
    }


    /* =====================================================
       MOBILE PAGINATION
    ===================================================== */

    .products-pagination-inner {

        align-items: center;

        flex-direction: column;

        justify-content: center;

        gap: 9px;
    }


    .products-pagination-info {

        text-align: center;
    }


    .products-pagination-links {

        width: 100%;

        justify-content: center;
    }


    .products-pagination-links nav > div > span,
    .products-pagination-links nav > div > a {

        min-width: 28px;

        height: 28px;

        padding:
            0
            6px;

        font-size: 0.625rem;
    }

}

</style>

@endpush