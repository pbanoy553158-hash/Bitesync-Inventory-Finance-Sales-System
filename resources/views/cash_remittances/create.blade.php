@extends('layouts.app')

@section('title', 'BiteSync | Record Cash Remittance')

@section('content')

<div class="cash-remittance-create-page">

    {{-- =========================================================
         TOPBAR
    ========================================================== --}}

    <div class="topbar">

        <div class="page-title">

            <small>
                Finance
            </small>

            <h1>
                Record Cash Remittance
            </h1>

            <p>
                Record the expected and actual cash amount for a remittance.
            </p>

        </div>


        {{-- SAME DATE FORMAT AS PURCHASE CREATE --}}

        <div class="date-box">

            <span class="date-icon">
                ◷
            </span>

            {{ now()->format('F d, Y') }}

        </div>

    </div>


    {{-- =========================================================
         BREADCRUMB
    ========================================================== --}}

    <div class="cash-remittance-breadcrumb">

        <a href="{{ route('cash-remittances.index') }}">
            Cash Remittance
        </a>

        <span>
            /
        </span>

        <strong>
            Record Remittance
        </strong>

    </div>


    {{-- =========================================================
         VALIDATION ERRORS
    ========================================================== --}}

    @if ($errors->any())

        <div class="form-alert form-alert-error">

            <div class="alert-icon">
                !
            </div>

            <div>

                <div class="alert-title">
                    Please check the form.
                </div>

                <ul class="alert-list">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================================
         CASH REMITTANCE FORM
    ========================================================== --}}

    <form
        method="POST"
        action="{{ route('cash-remittances.store') }}"
    >

        @csrf


        {{-- =====================================================
             REMITTANCE INFORMATION
        ====================================================== --}}

        <div class="form-panel">

            <div class="form-panel-header">

                <div>

                    <div class="form-panel-title">
                        Remittance Information
                    </div>

                    <div class="form-panel-subtitle">
                        Enter the expected and actual cash amount for this remittance.
                    </div>

                </div>

                <div class="form-panel-icon">
                    ₱
                </div>

            </div>


            <div class="form-content">

                <div class="remittance-form-grid">


                    {{-- REMITTANCE DATE --}}

                    <div class="form-group">

                        <label for="remittance_date">

                            Remittance Date

                            <span>
                                *
                            </span>

                        </label>

                        <input
                            type="date"
                            name="remittance_date"
                            id="remittance_date"
                            value="{{ old('remittance_date', now()->format('Y-m-d')) }}"
                            required
                        >

                    </div>


                    {{-- STATUS --}}

                    <div class="form-group">

                        <label for="status">

                            Status

                            <span>
                                *
                            </span>

                        </label>

                        <select
                            name="status"
                            id="status"
                            required
                        >

                            @foreach (
                                ['Recorded', 'Verified', 'Voided']
                                as $status
                            )

                                <option
                                    value="{{ $status }}"
                                    {{ old('status', 'Recorded') === $status ? 'selected' : '' }}
                                >
                                    {{ $status }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- EXPECTED AMOUNT --}}

                    <div class="form-group">

                        <label for="expectedAmount">

                            Expected Amount

                            <span>
                                *
                            </span>

                        </label>

                        <div class="currency-input">

                            <span>
                                ₱
                            </span>

                            <input
                                type="number"
                                name="expected_amount"
                                id="expectedAmount"
                                value="{{ old('expected_amount', '0.00') }}"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                required
                            >

                        </div>

                    </div>


                    {{-- ACTUAL AMOUNT --}}

                    <div class="form-group">

                        <label for="actualAmount">

                            Actual Amount

                            <span>
                                *
                            </span>

                        </label>

                        <div class="currency-input">

                            <span>
                                ₱
                            </span>

                            <input
                                type="number"
                                name="actual_amount"
                                id="actualAmount"
                                value="{{ old('actual_amount', '0.00') }}"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                required
                            >

                        </div>

                    </div>


                    {{-- REFERENCE NUMBER --}}

                    <div class="form-group">

                        <label for="reference_no">
                            Reference Number
                        </label>

                        <input
                            type="text"
                            name="reference_no"
                            id="reference_no"
                            value="{{ old('reference_no') }}"
                            maxlength="100"
                            placeholder="Optional reference number"
                        >

                    </div>


                    {{-- CALCULATED VARIANCE --}}

                    <div class="form-group">

                        <label>
                            Calculated Variance
                        </label>

                        <div class="variance-preview">

                            <strong id="variancePreview">
                                ₱0.00
                            </strong>

                        </div>

                    </div>


                    {{-- REMARKS --}}

                    <div class="form-group full">

                        <label for="remarks">
                            Remarks
                        </label>

                        <textarea
                            name="remarks"
                            id="remarks"
                            rows="4"
                            maxlength="500"
                            placeholder="Enter additional notes or remarks..."
                        >{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             ACTION BUTTONS
        ====================================================== --}}

        <div class="form-actions">

            <a
                href="{{ route('cash-remittances.index') }}"
                class="cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="save-button"
            >

                <span>
                    +
                </span>

                Record Remittance

            </button>

        </div>

    </form>

</div>

@endsection


@push('styles')

<style>

/* ================================================================
   PAGE
================================================================ */

.cash-remittance-create-page {
    width: 100%;
    max-width: 1120px;
    margin: 0 auto;
    padding-bottom: 30px;
}


/* ================================================================
   TOP BAR
   SAME AS PURCHASE CREATE
================================================================ */

.cash-remittance-create-page .topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 14px;
}

.cash-remittance-create-page .page-title small {
    display: block;
    margin-bottom: 5px;
    color: #a9825b;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.cash-remittance-create-page .page-title h1 {
    margin: 0;
    color: var(--dark);
    font-size: 29px;
    font-weight: 700;
    line-height: 1.15;
    letter-spacing: -0.045rem;
}

.cash-remittance-create-page .page-title p {
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.5;
    font-weight: 400;
}


/* ================================================================
   DATE
   EXACT PURCHASE CREATE FORMAT
================================================================ */

.cash-remittance-create-page .date-box {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    min-width: 145px;
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: white;
    color: var(--muted);
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow:
        0 3px 12px
        rgba(43, 31, 23, .025);
}

.cash-remittance-create-page .date-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    border-radius: 8px;
    background: var(--orange-light);
    color: var(--orange);
    font-size: 17px;
}


/* ================================================================
   BREADCRUMB
================================================================ */

.cash-remittance-breadcrumb {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 17px;
    color: #96877b;
    font-size: 10px;
    font-weight: 600;
    line-height: 1.3;
}

.cash-remittance-breadcrumb a {
    color: #a16e42;
    font-weight: 600;
    text-decoration: none;
    transition: color .18s ease;
}

.cash-remittance-breadcrumb a:hover {
    color: #7d4e29;
}

.cash-remittance-breadcrumb span {
    color: #c2b5aa;
}

.cash-remittance-breadcrumb strong {
    color: #6f6259;
    font-weight: 600;
}


/* ================================================================
   ALERT
================================================================ */

.cash-remittance-create-page .form-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 16px;
    padding: 11px 13px;
    border-radius: 10px;
    font-size: 11px;
    line-height: 1.45;
    font-weight: 400;
}

.cash-remittance-create-page .form-alert-error {
    border: 1px solid #efd4cf;
    background: #fff7f5;
    color: #7d433b;
}

.cash-remittance-create-page .alert-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    border-radius: 7px;
    background: #f3d4cf;
    color: #8a4339;
    font-size: 11px;
    font-weight: 700;
}

.cash-remittance-create-page .alert-title {
    margin-bottom: 4px;
    font-size: 11px;
    font-weight: 700;
}

.cash-remittance-create-page .alert-list {
    margin: 4px 0 0;
    padding-left: 16px;
    line-height: 1.45;
}

.cash-remittance-create-page .alert-list li {
    margin-bottom: 2px;
    font-size: 11px;
    font-weight: 400;
}


/* ================================================================
   FORM PANEL
================================================================ */

.cash-remittance-create-page .form-panel {
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 15px;
    background: white;
    box-shadow:
        0 4px 16px
        rgba(43, 31, 23, 0.035);
}


/* ================================================================
   PANEL HEADER
================================================================ */

.cash-remittance-create-page .form-panel-header {
    min-height: 65px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 14px 19px;
    border-bottom: 1px solid var(--border);
    background: white;
}

.cash-remittance-create-page .form-panel-title {
    color: var(--dark);
    font-size: 17px;
    line-height: 1.3;
    font-weight: 700;
}

.cash-remittance-create-page .form-panel-subtitle {
    margin-top: 3px;
    color: var(--muted);
    font-size: 11px;
    line-height: 1.4;
    font-weight: 400;
}

.cash-remittance-create-page .form-panel-icon {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: var(--orange-light);
    color: var(--orange);
    font-size: 13px;
    font-weight: 700;
}


/* ================================================================
   FORM CONTENT
================================================================ */

.cash-remittance-create-page .form-content {
    padding: 18px;
}


/* ================================================================
   FORM GRID
================================================================ */

.cash-remittance-create-page .remittance-form-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);
    gap: 17px;
}

.cash-remittance-create-page .form-group.full {
    grid-column: 1 / -1;
}


/* ================================================================
   FORM GROUP
================================================================ */

.cash-remittance-create-page .form-group {
    min-width: 0;
    margin-bottom: 0;
}

.cash-remittance-create-page .form-group label {
    display: block;
    margin-bottom: 6px;
    color: #4c3c31;
    font-size: 11px;
    line-height: 1.35;
    font-weight: 600;
}

.cash-remittance-create-page .form-group label span {
    color: #b65f45;
    font-weight: 600;
}


/* ================================================================
   INPUTS
================================================================ */

.cash-remittance-create-page .form-group input,
.cash-remittance-create-page .form-group select,
.cash-remittance-create-page .form-group textarea {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #ded4cb;
    border-radius: 8px;
    background: white;
    color: var(--text);
    font-family: inherit;
    font-size: 12px;
    line-height: 1.4;
    font-weight: 400;
    outline: none;
    transition:
        border-color .18s ease,
        box-shadow .18s ease;
}

.cash-remittance-create-page .form-group input,
.cash-remittance-create-page .form-group select {
    height: 39px;
    padding: 0 11px;
}

.cash-remittance-create-page .form-group textarea {
    min-height: 100px;
    padding: 10px 11px;
    resize: vertical;
    line-height: 1.5;
}

.cash-remittance-create-page .form-group input::placeholder,
.cash-remittance-create-page .form-group textarea::placeholder {
    color: #b8aaa0;
    font-weight: 400;
}

.cash-remittance-create-page .form-group input:focus,
.cash-remittance-create-page .form-group select:focus,
.cash-remittance-create-page .form-group textarea:focus {
    border-color: #d2a47b;
    box-shadow:
        0 0 0 3px
        rgba(196, 122, 58, .075);
}

.cash-remittance-create-page .form-group select {
    cursor: pointer;
}


/* ================================================================
   NUMBER INPUT
================================================================ */

.cash-remittance-create-page input[type="number"] {
    appearance: textfield;
}

.cash-remittance-create-page input[type="number"]::-webkit-inner-spin-button,
.cash-remittance-create-page input[type="number"]::-webkit-outer-spin-button {
    opacity: .6;
}


/* ================================================================
   CURRENCY INPUT
================================================================ */

.cash-remittance-create-page .currency-input {
    position: relative;
}

.cash-remittance-create-page .currency-input > span {
    position: absolute;
    top: 50%;
    left: 11px;
    z-index: 2;
    transform: translateY(-50%);
    color: #8c684b;
    font-size: 12px;
    line-height: 1;
    font-weight: 600;
    pointer-events: none;
}

.cash-remittance-create-page .currency-input input {
    padding-left: 27px;
}


/* ================================================================
   VARIANCE
================================================================ */

.cash-remittance-create-page .variance-preview {
    display: flex;
    align-items: center;
    width: 100%;
    height: 39px;
    box-sizing: border-box;
    padding: 0 11px;
    border: 1px solid #ded4cb;
    border-radius: 8px;
    background: #fcfaf8;
}

.cash-remittance-create-page #variancePreview {
    color: #5d8b67;
    font-size: 14px;
    line-height: 1;
    font-weight: 800;
}


/* ================================================================
   ACTION BUTTONS
================================================================ */

.cash-remittance-create-page .form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding-top: 17px;
    padding-bottom: 4px;
}

.cash-remittance-create-page .cancel-button,
.cash-remittance-create-page .save-button {
    min-height: 37px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0 14px;
    border-radius: 8px;
    font-family: inherit;
    font-size: 11px;
    line-height: 1;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}


/* ================================================================
   CANCEL
================================================================ */

.cash-remittance-create-page .cancel-button {
    border: 1px solid var(--border);
    background: white;
    color: var(--muted);
    transition:
        background-color .18s ease,
        border-color .18s ease,
        color .18s ease;
}

.cash-remittance-create-page .cancel-button:hover {
    border-color: #d4c6ba;
    background: #faf7f4;
    color: var(--dark);
}


/* ================================================================
   SAVE
================================================================ */

.cash-remittance-create-page .save-button {
    min-width: 135px;
    border: none;
    background:
        linear-gradient(
            135deg,
            var(--orange),
            var(--orange-dark)
        );
    color: white;
    box-shadow:
        0 4px 11px
        rgba(145, 97, 55, .14);
    transition:
        transform .18s ease,
        box-shadow .18s ease;
}

.cash-remittance-create-page .save-button:hover {
    background:
        linear-gradient(
            135deg,
            var(--orange-dark),
            var(--orange-dark)
        );

    transform: translateY(-1px);

    box-shadow:
        0 6px 14px
        rgba(145, 97, 55, .18);
}

.cash-remittance-create-page .save-button span {
    font-size: 13px;
    line-height: 1;
    font-weight: 400;
}


/* ================================================================
   MOBILE
================================================================ */

@media (max-width: 700px) {

    .cash-remittance-create-page .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .cash-remittance-create-page .date-box {
        align-self: flex-start;
    }

    .cash-remittance-create-page .remittance-form-grid {
        grid-template-columns: 1fr;
    }

    .cash-remittance-create-page .form-group.full {
        grid-column: auto;
    }

    .cash-remittance-create-page .form-content {
        padding: 15px;
    }

    .cash-remittance-create-page .form-panel-header {
        padding: 14px 15px;
    }

    .cash-remittance-create-page .form-actions {
        justify-content: stretch;
    }

    .cash-remittance-create-page .cancel-button,
    .cash-remittance-create-page .save-button {
        flex: 1;
    }

}


@media (max-width: 480px) {

    .cash-remittance-create-page .page-title h1 {
        font-size: 23px;
    }

    .cash-remittance-create-page .form-panel-title {
        font-size: 17px;
    }

    .cash-remittance-create-page .form-actions {
        flex-direction: column-reverse;
    }

    .cash-remittance-create-page .cancel-button,
    .cash-remittance-create-page .save-button {
        width: 100%;
    }

}

</style>

@endpush


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const expectedInput =
        document.getElementById('expectedAmount');

    const actualInput =
        document.getElementById('actualAmount');

    const variancePreview =
        document.getElementById('variancePreview');


    function updateVariance() {

        const expected =
            Number(expectedInput.value || 0);

        const actual =
            Number(actualInput.value || 0);

        const variance =
            actual - expected;


        let prefix = '';


        if (variance > 0) {

            prefix = '+';

        } else if (variance < 0) {

            prefix = '−';

        }


        variancePreview.textContent =
            prefix +
            '₱' +
            Math.abs(variance).toLocaleString(
                'en-PH',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );


        if (variance < 0) {

            variancePreview.style.color =
                '#b95d56';

        } else if (variance > 0) {

            variancePreview.style.color =
                '#637f9f';

        } else {

            variancePreview.style.color =
                '#5d8b67';

        }

    }


    expectedInput.addEventListener(
        'input',
        updateVariance
    );

    actualInput.addEventListener(
        'input',
        updateVariance
    );


    updateVariance();

});

</script>

@endpush