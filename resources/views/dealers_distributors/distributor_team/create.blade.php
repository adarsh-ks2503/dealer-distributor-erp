@extends('layouts.main')
@section('title', 'New Team - Singhal')
@section('content')

<main id="main" class="main">
    @if ($message = Session::get('error'))
        <div class="tt active">
            <div class="tt-content">
                <i class="fas fa-solid fa-check check"></i>
                <div class="message">
                    <span class="text text-1">Error</span>
                    <span class="text text-2"> {{ $message }}</span>
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="pg active"></div>
        </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="dashboard-header pagetitle">
        <h1>Distributor Team</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributor_team.index') }}">Distributor Team</a></li>
                <li class="breadcrumb-item active">New Team</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">New Team</h4>
                <a href="{{ route('distributor_team.index') }}" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back
                </a>
            </div>
            <form action="{{ route('distributor_team.store') }}" method="POST">
                @csrf
                <div class="card-body mt-3">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Team Details</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distributor Name: <span class="text-danger">*</span></label>
                            <select name="distributor_id" id="distributor_id" class="form-select custom-input" required>
                                <option value="">Select</option>
                                @foreach ($distributors as $distributor)
                                    <option value={{ $distributor->id }}>{{ $distributor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dealers: <span class="text-danger">*</span></label>

                            <div id="dealer-selection" class="dealer-selection-wrapper">
                                <div class="dealer-list available">
                                    <h6>
                                        <span><i class="fas fa-users text-primary me-1"></i>Available Dealers</span>
                                        <button type="button" class="btn btn-link btn-sm p-0" id="select-all-dealers">Select All</button>
                                    </h6>
                                    <div class="dealer-items" id="available-dealers"></div>
                                </div>

                                <div class="dealer-list selected">
                                    <h6>
                                        <span><i class="fas fa-user-check text-success me-1"></i>Selected Dealers</span>
                                        <button type="button" class="btn btn-link btn-sm p-0" id="remove-all-dealers">Remove All</button>
                                    </h6>
                                    <div class="dealer-items" id="selected-dealers"></div>
                                </div>
                            </div>

                            <div id="selected-dealer-ids"></div>

                            <small class="text-muted d-block mt-2">Note: Only active dealers from the same state as the selected distributor will be shown.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distributor Team Size:</label>
                            <input type="number" name="distributor_team_size" class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distributor Individual Order Limit (MT):</label>
                            <input type="number" name="distributor_individual_order_limit" class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dealer's Order Limit (MT):</label>
                            <input type="number" name="dealer_order_limit" class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distributor Total Order Limit (MT):</label>
                            <input type="number" name="distributor_order_limit" class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Remark:</label>
                            <input type="text" name="remark" class="form-control custom-input" placeholder="Enter Remark">
                        </div>

                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('distributor_team.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn custom-btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@endsection

@push('styles')
<style>
    /* General Styles */
    .custom-input {
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-size: 14.5px;
    }

    .custom-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    .custom-btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
    }

    .custom-btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        transform: translateY(-2px);
    }

    .card {
        border-radius: 12px;
        border: none;
    }

    .card-header {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    .form-label {
        font-size: 14px;
        color: #374151;
    }

    /* Dealer Selection Styles */
    .dealer-selection-wrapper {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .dealer-list {
        flex: 1;
        min-width: 280px;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        max-height: 320px;
        overflow-y: auto;
    }

    .dealer-list h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #334155;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dealer-list h6 .btn-link {
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        padding-right: 0;
    }

    .dealer-items {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dealer-item {
        background: white;
        border: 1px solid #cbd5e1;
        border-left: 5px solid #3b82f6;
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        cursor: pointer;
    }

    .dealer-item:hover {
        background: #eff6ff;
        border-color: #2563eb;
        transform: scale(1.02);
    }

    .dealer-item i {
        font-size: 16px;
        transition: all 0.2s ease;
        padding: 6px;
        border-radius: 50%;
        background-color: #e0f2fe;
        color: #2563eb;
    }

    .dealer-item i:hover {
        background-color: #bfdbfe;
        transform: scale(1.2);
    }

    .dealer-list.selected .dealer-item {
        border-left-color: #10b981;
    }

    .dealer-list.selected .dealer-item i {
        background-color: #dcfce7;
        color: #059669;
    }

    .dealer-list.selected .dealer-item i:hover {
        background-color: #bbf7d0;
        color: #047857;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // --- DOM Elements ---
    const distributorSelect = document.getElementById('distributor_id');
    const availableDealersContainer = document.getElementById('available-dealers');
    const selectedDealersContainer = document.getElementById('selected-dealers');
    const selectedDealerIdsContainer = document.getElementById('selected-dealer-ids');
    const selectAllBtn = document.getElementById('select-all-dealers');
    const removeAllBtn = document.getElementById('remove-all-dealers');

    const teamSizeInput = document.querySelector('input[name="distributor_team_size"]');
    const dealerOrderLimitInput = document.querySelector('input[name="dealer_order_limit"]');
    const distributorIndividualLimitInput = document.querySelector('input[name="distributor_individual_order_limit"]');
    const distributorTotalLimitInput = document.querySelector('input[name="distributor_order_limit"]');

    // --- State ---
    let availableDealers = [];
    let selectedDealers = [];
    let distributorIndividualLimit = 0;

    // --- Event Listeners ---
    distributorSelect.addEventListener('change', handleDistributorChange);
    selectAllBtn.addEventListener('click', selectAllDealers);
    removeAllBtn.addEventListener('click', removeAllDealers);

    // --- Functions ---
    async function handleDistributorChange() {
        const distributorId = this.value;
        resetDealers();

        if (!distributorId) return;

        try {
            // Fetch distributor details and dealers simultaneously
            const [distributorData, dealersData] = await Promise.all([
                fetch(`/get-distributor/${distributorId}`).then(res => res.json()),
                fetch(`/get-dealers-by-distributor/${distributorId}`).then(res => res.json())
            ]);

            // Update distributor limit
            distributorIndividualLimit = parseFloat(distributorData.order_limit || 0);
            distributorIndividualLimitInput.value = distributorIndividualLimit;

            // Update available dealers
            availableDealers = dealersData;

            renderDealers(); // This will also call updateTeamSizeAndLimits
        } catch (error) {
            console.error("Error fetching data:", error);
            availableDealersContainer.innerHTML = `<p class="text-danger">Failed to load dealers.</p>`;
        }
    }

    function resetDealers() {
        availableDealers = [];
        selectedDealers = [];
        distributorIndividualLimit = 0;
        distributorIndividualLimitInput.value = '';
        renderDealers();
    }

    function renderDealers() {
        availableDealersContainer.innerHTML = '';
        selectedDealersContainer.innerHTML = '';
        selectedDealerIdsContainer.innerHTML = '';

        availableDealers.forEach(dealer => {
            const item = createDealerItem(dealer, 'add');
            availableDealersContainer.appendChild(item);
        });

        selectedDealers.forEach(dealer => {
            const item = createDealerItem(dealer, 'remove');
            selectedDealersContainer.appendChild(item);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'dealers[]';
            input.value = dealer.id;
            selectedDealerIdsContainer.appendChild(input);
        });

        updateTeamSizeAndLimits();
    }

    function createDealerItem(dealer, action) {
        const div = document.createElement('div');
        div.className = 'dealer-item';
        div.dataset.dealerId = dealer.id;
        div.innerHTML = `
            <span>${dealer.name}</span>
            <i class="fas fa-${action === 'add' ? 'plus' : 'times'}"></i>
        `;
        div.querySelector('i').addEventListener('click', () => {
            if (action === 'add') selectDealer(dealer);
            else unselectDealer(dealer);
        });
        return div;
    }

    function selectDealer(dealer) {
        selectedDealers.push(dealer);
        availableDealers = availableDealers.filter(d => d.id !== dealer.id);
        renderDealers();
    }

    function unselectDealer(dealer) {
        availableDealers.push(dealer);
        selectedDealers = selectedDealers.filter(d => d.id !== dealer.id);
        renderDealers();
    }

    function selectAllDealers() {
        if (availableDealers.length === 0) return;
        selectedDealers.push(...availableDealers);
        availableDealers = [];
        renderDealers();
    }

    function removeAllDealers() {
        if (selectedDealers.length === 0) return;
        availableDealers.push(...selectedDealers);
        selectedDealers = [];
        renderDealers();
    }

    function updateTeamSizeAndLimits() {
        teamSizeInput.value = selectedDealers.length;

        if (selectedDealers.length === 0) {
            dealerOrderLimitInput.value = 0;
            distributorTotalLimitInput.value = distributorIndividualLimit;
            return;
        }

        const dealerIds = selectedDealers.map(d => d.id);

        fetch('/get-dealers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ dealer_ids: dealerIds })
        })
        .then(res => res.json())
        .then(data => {
            const totalDealerOrderLimit = data.reduce((sum, d) => sum + parseFloat(d.order_limit || 0), 0);
            dealerOrderLimitInput.value = totalDealerOrderLimit;
            distributorTotalLimitInput.value = distributorIndividualLimit + totalDealerOrderLimit;
        })
        .catch(error => console.error("Error updating limits:", error));
    }
});
</script>
@endpush
