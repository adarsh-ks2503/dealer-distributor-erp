@extends('layouts.main')
@section('title', 'Item Basic Prices Report - Singhal Steel')
@section('content')

    <style>
        /* For mobile: disable fixed columns forcefully */
        @media (max-width: 768px) {

            .dtfc-fixed-left,
            .dtfc-fixed-right {
                position: relative !important;
                transform: none !important;
                z-index: auto !important;
            }
        }
    </style>

    <main id="main" class="main">

        @include('partials.heading_cards.cards', [
            'heading' => 'Item Basic Prices Report',
            'sub_heading' => 'Item Basic Prices Report',
        ])

        <section class="section">
            <div class="card pt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                        </div>
                        <div class="col-md-6 col-sm-12 d-flex justify-content-end">
                            <div class="">
                                <button class="mr-2 btn btn-primary" type="button"
                                    onclick="filterButton(
                $('#filterFromdate').val(),
                $('#filterTodate').val(),
                // $('#DealersNameDropdown').val(),
                // $('#DistributorsNameDropdown').val(),
                // $('#OrderNumberDropdown').val(),
                $('#StateDropdown').val(),
                $('#filterType').val(),
            )">
                                    Filter
                                </button>
                                <button class="mr-2 btn btn-primary" type="button" id="resetButton">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="page-header">
                        <div class="row">
                            @include('partials.heading_cards.date')


                            {{-- <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Dealers</strong></label>
                                <select class="custom-select form-control" id="DealersNameDropdown">
                                    <option value="" disabled>Select Dealer</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($dealers as $dealer)
                                        <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Distributor</strong></label>
                                <select class="custom-select form-control" id="DistributorsNameDropdown">
                                    <option value="" disabled>Select Distributor</option>
                                    <option value="all" selected>All</option>

                                    @foreach ($distributors as $distributor)
                                        <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Order Number</strong></label>
                                <select class="custom-select form-control" id="OrderNumberDropdown">
                                    <option value="" disabled>Select</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                            {{-- <div class="col-md-2 col-sm-12 mt-3" id="dealerDropdownWrapper" style="display:none;">
                                <label for="" class="mb-2"><strong>Dealer Name</strong></label>
                                <select class="custom-select form-control" name="dealer_id" id="dealerDropdown">
                                    <option value="all" selected>All</option>
                                    @foreach ($dealers as $dealer)
                                        <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                            {{-- <div class="col-md-2 col-sm-12 mt-3" id="distributorDropdownWrapper" style="display:none;">
                                <label for="" class="mb-2"><strong>Distributor Name</strong></label>
                                <select class="custom-select form-control" name="distributor_id" id="distributorDropdown">
                                    <option value="all" selected>All</option>
                                    @foreach ($distributers as $distributer)
                                        <option value="{{ $distributer->id }}">{{ $distributer->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}


                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>State</strong></label>
                                <select class="custom-select form-control" id="StateDropdown">
                                    <option value="" disabled>Select</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Status</strong></label>
                                <select class="custom-select form-control" name="item_name" id="filterType">
                                    <option value="" disabled>Select Status</option>
                                    <option value="all" selected>All</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    {{-- <option value="rejected">Rejected</option> --}}
                                    {{-- @foreach ($saudas as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $selectedId) selected @endif>{{ $item->sauda_number }}
                                        </option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card pt-4">
                        <div class="card-body">
                            <!-- Table with stripped rows -->
                            <table id="item_price_report_table" class="display stripe row-border order-column"
                                style="width:100%">
                                {{-- <div>
                                    <span style="color: #212529; font-weight: bold;">■</span> Current Price
                                    <br>
                                    <span style="color: rgb(0, 140, 255); font-weight: bold;">■</span> Old Price
                                    <br>
                                </div> --}}

                                <thead>
                                    <tr>
                                        {{-- <th class="text__left">Sl. No</th> --}}
                                        <th class="text__left">S. NO</th>
                                        <th class="text__left">ITEM</th>
                                        <th class="text__left">STATE</th>
                                        <th class="text__left">MARKET </br> BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">DISTRIBUTOR </br> BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">DEALER BASIC <br> PRICE(₹/MT)</th>
                                        <th class="text__left">APPROVAL TIME</th>
                                        <th class="text__left">APPROVED BY</th>
                                        <th class="text__left">CREATED AT</th>
                                        <th class="text__left">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>




        <div class="modal fade" id="priceHistoryModal" tabindex="-1" aria-labelledby="priceHistoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Price History - <span id="itemInfoPlaceholder"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="priceHistoryLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading history...</p>
                        </div>
                        <div id="priceHistoryContent" style="display: none;">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date Changed</th>
                                        <th>Market Price (₹)</th>
                                        <th>Distributor Price (₹)</th>
                                        <th>Dealer Price (₹)</th>
                                        <th>Changed By</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="priceHistoryTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </main>
    <script>
        // Define the filterButton function first
        function filterButton(filterFromdate, filterTodate, StateDropdown, filterType) {
            $.ajax({
                type: 'POST',
                url: '{{ route('get_item_price.report') }}',
                data: {
                    from_date: filterFromdate,
                    to_date: filterTodate,
                    // dealer_name: DealersNameDropdown,
                    // distributer_name: DistributorsNameDropdown,
                    // order_number: OrderNumberDropdown,
                    state: StateDropdown,
                    type: filterType,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    var table = $('#item_price_report_table').DataTable();
                    table.clear().draw();
                    let rows = [];

                    res.forEach(function(item, index) {
                        console.log(item);
                        // Start by setting the default value to 'N/A'.
                        let fullFormatted = 'N/A';

                        // Find a valid date from either of the two fields.
                        const dateValue = item.approval_date;

                        // Only run the formatting logic if a valid date was found.
                        if (dateValue) {
                            const dt = new Date(dateValue);
                            const formattedDate = dt.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }).replace(/ /g, ' ');

                            const time = dt.toLocaleTimeString('en-GB', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }).toLowerCase();

                            // If a date was formatted, update the variable.
                            fullFormatted = `${formattedDate} ${time}`;
                        }
                        // Default values
                        // let fsrBasic = '0';
                        // let lightChannelBasic = '0';
                        // let heavyChannelBasic = '0';


                        // let agreedPrice = parseFloat(item.agreed_basic_price ?? 0);
                        // let insurance = parseFloat(item.insurance_charge ?? 0);
                        // let loading = parseFloat(item.loading_charge ?? 0);

                        // let totalAmount = agreedPrice + insurance + loading;
                        // if (item.status === 'Approved' || item.status === 'Old') {
                        //     marketPriceCell = `<span class="view-price-history-btn"
                        //       data-price-id="${item.id}"
                        //       data-item-info="${item.item_name.item_name} - ${item.state_name.state}"
                        //       style="color: #0d6efd; cursor: pointer;">
                        //       ${item.market_basic_price ?? '0'}
                        //    </span>`;
                        // } else {
                        //     // If status is 'Pending' (or anything else), just show the plain number
                        //     marketPriceCell = item.market_basic_price ?? '0';
                        // }

                        const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };


                        // --- Phir 'if' condition ---
if (item.status === 'Approved' || item.status === 'Old') {
    // --- BADLAV 1: YAHAN FORMATTING ADD KI GAYI HAI ---
    const formattedPrice = item.market_basic_price != null 
        ? Number(item.market_basic_price).toLocaleString('en-IN', currencyFormat) 
        : '0.00';

    marketPriceCell = `<span class="view-price-history-btn"
                          data-price-id="${item.id}"
                          data-item-info="${item.item_name.item_name} - ${item.state_name.state}"
                          style="color: #0d6efd; cursor: pointer;">
                          ${formattedPrice}
                       </span>`;
} else {
    // --- BADLAV 2: YAHAN BHI FORMATTING ADD KI GAYI HAI ---
    // If status is 'Pending', show formatted number without link
    marketPriceCell = item.market_basic_price != null 
        ? Number(item.market_basic_price).toLocaleString('en-IN', currencyFormat) 
        : '0.00';
}

                        let formattedCreatedAt = new Date(item.created_at).toLocaleString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });

                        // let formattedApprovalTime = new Date(item.approval_date).toLocaleString('en-GB', {
                        //     day: '2-digit',
                        //     month: 'short',
                        //     year: 'numeric',
                        //     hour: '2-digit',
                        //     minute: '2-digit',
                        //     hour12: true
                        // });

                        rows.push([
                            index + 1, // Sl. No

                            item.item_name.item_name ?? 'N/A', // Item

                            item.state_name.state ?? 'N/A', // State

                            // item.market_basic_price ?? '0', // Market Basic Price (₹/MT)
                            marketPriceCell,

                            // item.distributor_basic_price ??
                            // '0', // Distributor Basic Price (₹/MT)

                            // item.dealer_basic_price ?? '0', // Dealer Basic Price (₹/MT)

                            item.distributor_basic_price != null ? Number(item.distributor_basic_price).toLocaleString('en-IN', currencyFormat) : '0.00',

// Dealer Basic Price
item.dealer_basic_price != null ? Number(item.dealer_basic_price).toLocaleString('en-IN', currencyFormat) : '0.00',

                            fullFormatted ?? 'N/A', // Approval Time
                            // formattedApprovalTime,

                            // item.approved_by ?? 'N/A' // Approved By
                            item.approved_by ?? 'N/A',

                            // item.created_at ?? 'N/A',
                            formattedCreatedAt,

                            item.status ?? 'N/A',
                        ]);

                        // if (item.record_type === 'history') {
                        //     // If the mark is found, ONLY THEN is the CSS class applied
                        //     $(newRow).addClass('history-row');
                        // }

                    });
                    table.rows.add(rows).draw();

                    $('#dispatch_items_report_table tbody tr').each(function() {
                        $(this).find(
                            'td:eq(0), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11), td:eq(11), td:eq(12), td:eq(13)'
                        ).addClass(
                            'text__left');
                    });
                },
                // error: function(xhr, status, error) {
                //     console.error("AJAX request failed:", status, error);
                // }
            });
        }

        $(document).ready(function() {
            // Call filterButton when document is ready
            filterButton(
                $('#filterFromdate').val(),
                $('#filterTodate').val(),
                // $('#DealerNameDropdown').val(),
                // $('#DistributorsNameDropdown').val(),
                // $('#OrderNumberDropdown').val(),
                $('#StateDropdown').val(),
                $('#filterType').val()
            );

            // Filter button onClick handler
            $('#filterButton').click(function() {
                filterButton(
                    $('#filterFromdate').val(),
                    $('#filterTodate').val(),
                    // $('#DealersNameDropdown').val(),
                    // $('#DistributorsNameDropdown').val(),
                    // $('#OrderNumberDropdown').val(),
                    $('#StateDropdown').val(),
                    $('#filterType').val()
                );
            });

            // Reset button functionality
            $('#resetButton').click(function() {
                location.reload();
            });

            // Select2 initialization
            // $("#DealersNameDropdown").select2();
            // $("#DistributorsNameDropdown").select2();
            // $("#OrderNumberDropdown").select2();
            $("#StateDropdown").select2();
            $("#filterType").select2();
        });
    </script>

    <style>
        .history-row td {
            /* color: #555; */
            color: rgb(0, 140, 255);
            /* opacity: 0.7; */
        }
    </style>





    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Modal ke elements ko select karein
            const historyModal = new bootstrap.Modal(document.getElementById('priceHistoryModal'));
            const loader = document.getElementById('priceHistoryLoader');
            const content = document.getElementById('priceHistoryContent');
            const tbody = document.getElementById('priceHistoryTableBody');
            const itemInfoPlaceholder = document.getElementById('itemInfoPlaceholder');

            // Click event ko handle karein
            $('#item_price_report_table tbody').on('click', '.view-price-history-btn', function(event) {
                event.preventDefault();
                const priceId = this.getAttribute('data-price-id');
                const itemInfo = this.getAttribute('data-item-info');

                // Modal taiyaar karein
                itemInfoPlaceholder.textContent = itemInfo;
                loader.style.display = 'block';
                content.style.display = 'none';
                tbody.innerHTML = '';

                // API se price history fetch karein
                fetch(`/reports/item-price/${priceId}/history`)
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach((historyItem, index) => {
                                // let changeDate = new Date(historyItem.status_changed_at).toLocaleString('en-GB');
                                let changeDate = new Date(historyItem.status_changed_at)
                                    .toLocaleDateString('en-GB', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit',
                                        hour12: true
                                    });

                                    const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
                                const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${changeDate}</td>
                                <td>${historyItem.market_basic_price != null ? Number(historyItem.market_basic_price).toLocaleString('en-IN', currencyFormat) : '0.00'}</td>
<td>${historyItem.distributor_basic_price != null ? Number(historyItem.distributor_basic_price).toLocaleString('en-IN', currencyFormat) : '0.00'}</td>
<td>${historyItem.dealer_basic_price != null ? Number(historyItem.dealer_basic_price).toLocaleString('en-IN', currencyFormat) : '0.00'}</td>
                                <td>${historyItem.status_changed_by || 'N/A'}</td>
                                <td>${historyItem.status || 'N/A'}</td>
                            </tr>
                        `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="7" class="text-center">No price history found.</td></tr>';
                        }
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching price history:', error);
                        tbody.innerHTML =
                            '<tr><td colspan="7" class="text-center text-danger">Failed to load history.</td></tr>';
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    });

                historyModal.show();
            });
        });
    </script>


@endsection
