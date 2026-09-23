@extends('layouts.app')

@section('title', 'Edit Cash Remittance')

@section('content')

<style>

.cash-edit-page {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    padding-bottom: 30px;
}

.cash-edit-header {
    margin-bottom: 20px;
}

.cash-edit-header small {
    display: block;
    margin-bottom: 5px;
    color: #a9825b;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.cash-edit-header h1 {
    margin: 0;
    color: #241a14;
    font-size: 29px;
    font-weight: 700;
}

.cash-edit-header p {
    margin: 6px 0 0;
    color: #8b8179;
    font-size: 12px;
}

.cash-edit-panel {
    border: 1px solid #e4dcd4;
    border-radius: 14px;
    background: white;
    overflow: hidden;
}

.cash-edit-section {
    padding: 19px;
    border-bottom: 1px solid #e4dcd4;
}

.cash-edit-section:last-child {
    border-bottom: 0;
}

.cash-edit-section h2 {
    margin: 0;
    color: #241a14;
    font-size: 14px;
    font-weight: 700;
}

.cash-edit-section p {
    margin: 4px 0 17px;
    color: #8b8179;
    font-size: 10px;
}

.cash-edit-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.cash-edit-field.full {
    grid-column: 1 / -1;
}

.cash-edit-field label {
    display: block;
    margin-bottom: 6px;
    color: #625951;
    font-size: 10px;
    font-weight: 700;
}

.cash-edit-field input,
.cash-edit-field select,
.cash-edit-field textarea {
    width: 100%;
    border: 1px solid #ddd4cc;
    border-radius: 8px;
    color: #3f352e;
    background: white;
    font-family: inherit;
    font-size: 11px;
    outline: none;
}

.cash-edit-field input,
.cash-edit-field select {
    height: 40px;
    padding: 0 11px;
}

.cash-edit-field textarea {
    min-height: 100px;
    padding: 11px;
    resize: vertical;
}

.cash-edit-field input:focus,
.cash-edit-field select:focus,
.cash-edit-field textarea:focus {
    border-color: #c47a3a;
    box-shadow: 0 0 0 3px rgba(196,122,58,.08);
}

.cash-error {
    margin-bottom: 17px;
    padding: 11px 14px;
    border: 1px solid #efd4d1;
    border-radius: 9px;
    background: #fdf5f4;
    color: #b95d56;
    font-size: 10px;
}

.cash-error ul {
    margin: 0;
    padding-left: 17px;
}

.cash-variance-preview {
    padding: 15px;
    border: 1px solid #eee6de;
    border-radius: 10px;
    background: #fcfaf7;
}

.cash-variance-label {
    color: #8b8179;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
}

#variancePreview {
    margin-top: 6px;
    font-size: 21px;
    font-weight: 800;
}

.cash-edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 19px;
    background: #fbf9f6;
}

.cash-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    padding: 0 16px;
    border: 1px solid #ddd4cc;
    border-radius: 8px;
    background: white;
    color: #756b63;
    text-decoration: none;
    font-size: 10px;
    font-weight: 700;
}

.cash-cancel:hover {
    color: #241a14;
    border-color: #c9beb4;
}

.cash-update {
    height: 40px;
    padding: 0 17px;
    border: 0;
    border-radius: 8px;
    background: #c47a3a;
    color: white;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
}

.cash-update:hover {
    background: #aa6830;
}

@media (max-width: 700px) {

    .cash-edit-grid {
        grid-template-columns: 1fr;
    }

    .cash-edit-field.full {
        grid-column: auto;
    }

    .cash-edit-actions {
        flex-direction: column-reverse;
    }

}

</style>


<div class="cash-edit-page">

    <div class="cash-edit-header">

        <small>
            Finance
        </small>

        <h1>
            Edit Cash Remittance
        </h1>

        <p>
            Update the recorded cash remittance information.
        </p>

    </div>


    @if ($errors->any())

        <div class="cash-error">

            <ul>

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('cash-remittances.update', $cashRemittance) }}"
        class="cash-edit-panel"
    >

        @csrf

        @method('PUT')


        <div class="cash-edit-section">

            <h2>
                Remittance Information
            </h2>

            <p>
                Update the remittance details and reconciliation amounts.
            </p>


            <div class="cash-edit-grid">


                {{-- REMITTANCE DATE --}}

                <div class="cash-edit-field">

                    <label for="remittance_date">
                        Remittance Date
                    </label>

                    <input
                        type="date"
                        id="remittance_date"
                        name="remittance_date"
                        value="{{ old(
                            'remittance_date',
                            optional($cashRemittance->remittance_date)->format('Y-m-d')
                        ) }}"
                        required
                    >

                </div>


                {{-- STATUS --}}

                <div class="cash-edit-field">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        @foreach (
                            ['Recorded', 'Verified', 'Voided']
                            as $status
                        )

                            <option
                                value="{{ $status }}"
                                @selected(
                                    old(
                                        'status',
                                        $cashRemittance->status
                                    ) === $status
                                )
                            >
                                {{ $status }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- EXPECTED AMOUNT --}}

                <div class="cash-edit-field">

                    <label for="expectedAmount">
                        Expected Amount
                    </label>

                    <input
                        type="number"
                        name="expected_amount"
                        id="expectedAmount"
                        value="{{ old(
                            'expected_amount',
                            $cashRemittance->expected_amount
                        ) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                {{-- ACTUAL AMOUNT --}}

                <div class="cash-edit-field">

                    <label for="actualAmount">
                        Actual Amount
                    </label>

                    <input
                        type="number"
                        name="actual_amount"
                        id="actualAmount"
                        value="{{ old(
                            'actual_amount',
                            $cashRemittance->actual_amount
                        ) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                {{-- REFERENCE NUMBER --}}

                <div class="cash-edit-field">

                    <label for="reference_no">
                        Reference Number
                    </label>

                    <input
                        type="text"
                        id="reference_no"
                        name="reference_no"
                        value="{{ old(
                            'reference_no',
                            $cashRemittance->reference_no
                        ) }}"
                        maxlength="100"
                    >

                </div>


                {{-- VARIANCE PREVIEW --}}

                <div class="cash-edit-field">

                    <div class="cash-variance-preview">

                        <div class="cash-variance-label">
                            Calculated Variance
                        </div>

                        <div id="variancePreview">
                            ₱0.00
                        </div>

                    </div>

                </div>


                {{-- REMARKS --}}

                <div class="cash-edit-field full">

                    <label for="remarks">
                        Remarks
                    </label>

                    <textarea
                        id="remarks"
                        name="remarks"
                        maxlength="500"
                    >{{ old(
                        'remarks',
                        $cashRemittance->remarks
                    ) }}</textarea>

                </div>

            </div>

        </div>


        {{-- ACTIONS --}}

        <div class="cash-edit-actions">

            <a
                href="{{ route(
                    'cash-remittances.show',
                    $cashRemittance
                ) }}"
                class="cash-cancel"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="cash-update"
            >
                Save Changes
            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

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

    }
);

</script>

@endsection