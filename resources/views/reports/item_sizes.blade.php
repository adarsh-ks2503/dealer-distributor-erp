@extends('layouts.main')
@section('title', 'Item Size Report - Singhal Steel')
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
            'heading' => 'Item Size Report',
            'sub_heading' => 'Item Size Report',
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
                $('#HsnCodeDropdown').val(),
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
                                <label for="" class="mb-2"><strong>HSN Code</strong></label>
                                <select class="custom-select form-control" id="HsnCodeDropdown">
                                    <option value="" disabled>Select</option>
                                    <option value="all" selected>All</option>
                                    {{-- @foreach ($itemSizes as $hsn_codes)
                                        <option value="{{ $hsn_codes->hsn_code }}">{{ $hsn_codes->hsn_code }}</option>
                                    @endforeach --}}
                                    @foreach ($hsnCodes as $hsn)
                                        <option value="{{ $hsn }}">{{ $hsn }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Status</strong></label>
                                <select class="custom-select form-control" name="item_name" id="filterType">
                                    <option value="" disabled>Select Status</option>
                                    <option value="all" selected>All</option>
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
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
                            <table id="item_sizes_report_table" class="display stripe row-border order-column"
                                style="width:100%">
                                {{-- <div>
                                    <span style="color: #212529; font-weight: bold;">■</span> Current Sizes
                                    <br>
                                    <span style="color: rgb(0, 140, 255); font-weight: bold;">■</span> Old Sizes
                                    <br>
                                </div> --}}
                                <thead>
                                    <tr>
                                        <th>S. NO</th>
                                        <th>ITEM</th>
                                        <th class="text__left">SIZE(mm)</th>
                                        <th class="text__left">RATE</th>
                                        <th>HSN CODE</th>
                                        <th>REMARK</th>
                                        <th>APPROVAL TIME</th>
                                        <th>APPROVED BY</th>
                                        <th>CREATED AT</th>
                                        <th>STATUS</th>
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


        <div class="modal fade" id="sizeHistoryModal" tabindex="-1" aria-labelledby="sizeHistoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Size & Rate History - <span id="itemSizeInfoPlaceholder"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="sizeHistoryLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading history...</p>
                        </div>
                        <div id="sizeHistoryContent" style="display: none;">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date Changed</th>
                                        <th>HSN Code</th>
                                        <th>Rate (₹)</th>
                                        <th>Changed By</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="sizeHistoryTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </main>
    <script>
        // Define the filterButton function first
        function filterButton(filterFromdate, filterTodate, HsnCodeDropdown, filterType) {
            $.ajax({
                type: 'POST',
                url: '{{ route('get_item_sizes.report') }}',
                data: {
                    from_date: filterFromdate,
                    to_date: filterTodate,
                    // dealer_name: DealersNameDropdown,
                    // distributer_name: DistributorsNameDropdown,
                    // order_number: OrderNumberDropdown,
                    hsn_code: HsnCodeDropdown,
                    type: filterType,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    var table = $('#item_sizes_report_table').DataTable();
                    table.clear().draw();
                    let rows = [];

                    res.forEach(function(item, index) {
                        console.log(item);
                        // Start by setting the default value to 'N/A'.
                        let fullFormatted = 'N/A';

                        // Find a valid date from either of the two fields.
                        const dateValue = item.approval_time;

                        // Only run the formatting logic if a valid date was found.
                        if (dateValue) {
                            const dt = new Date(dateValue);
                            const formattedDate = dt.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }).replace(/ /g, '-');

                            const time = dt.toLocaleTimeString('en-GB', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }).toLowerCase();

                            // If a date was formatted, update the variable.
                            fullFormatted = `${formattedDate} ${time}`;
                        }

                        const dateValue2 = item.created_at;

                        // Only run the formatting logic if a valid date was found.
                        if (dateValue2) {
                            const dt2 = new Date(dateValue2);
                            const formattedDate2 = dt2.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }).replace(/ /g, '-');

                            const time2 = dt2.toLocaleTimeString('en-GB', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }).toLowerCase();

                            // If a date was formatted, update the variable.
                            fullFormatted2 = `${formattedDate2} ${time2}`;
                        }
                        // Default values
                        // let fsrBasic = '0';
                        // let lightChannelBasic = '0';
                        // let heavyChannelBasic = '0';


                        // let agreedPrice = parseFloat(item.agreed_basic_price ?? 0);
                        // let insurance = parseFloat(item.insurance_charge ?? 0);
                        // let loading = parseFloat(item.loading_charge ?? 0);

                        // let totalAmount = agreedPrice + insurance + loading;
                        // if (item.status === 'Active' || item.status === 'Inactive') {
                        //     rateCell = `<span" class="view-size-history-btn"
                        //                data-size-id="${item.id}"
                        //                data-item-info="${item.item_name.item_name} - ${item.size}mm"
                        //                style="color: #0d6efd; cursor: pointer;">
                        //                ${item.rate ?? '0'}
                        //             </span>`;
                        // } else {
                        //     // If status is 'Pending' (or anything else), just show the plain number
                        //     rateCell = item.rate ?? '0';
                        // }

                        const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };

                        if (item.status === 'Active' || item.status === 'Inactive') {
    // --- BADLAV 1: YAHAN `currencyFormat` VARIABLE KA ISTEMAL KAREIN ---
    const formattedRate = item.rate != null 
        ? Number(item.rate).toLocaleString('en-IN', currencyFormat) // <-- BADLAV YAHAN
        : '₹0.00'; // Fallback value mein bhi symbol add karein

    rateCell = `<span class="view-size-history-btn"
                    data-size-id="${item.id}"
                    data-item-info="${item.item_name.item_name} - ${item.size}mm"
                    style="color: #0d6efd; cursor: pointer;">
                    ${formattedRate}
                </span>`;
} else {
    // --- BADLAV 2: YAHAN BHI `currencyFormat` VARIABLE KA ISTEMAL KAREIN ---
    // If status is 'Pending', show formatted number without link
    rateCell = item.rate != null 
        ? Number(item.rate).toLocaleString('en-IN', currencyFormat) // <-- BADLAV YAHAN
        : '₹0.00';
}

                        rows.push([
                            index + 1, // Sl. No

                            item.item_name?.item_name ?? 'N/A', // Item Name
                            item.size ?? 'N/A', // Size
                            // item.rate ?? '0', // Rate
                            rateCell,
                            item.hsn_code ?? 'N/A', // HSN Code
                            item.remarks ?? 'N/A', // Remarks
                            // item.approval_time ?? 'N/A', // Approval Time
                            fullFormatted, // Approval Time
                            item.approved_by ?? 'N/A', // Approved By
                            fullFormatted2, // Approved By
                            item.status ?? 'N/A', // Status
                        ]);

                        // if (item.record_type === 'history') {
                        //     // If the mark is found, ONLY THEN is the CSS class applied
                        //     $(newRow).addClass('history-row');
                        // }



                        // $(newRow).find(
                        //     'td:eq(0), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11)'
                        // ).addClass(
                        //     'text__left');
                    });
                    table.rows.add(rows).draw();
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
                $('#HsnCodeDropdown').val(),
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
                    $('#HsnCodeDropdown').val(),
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
            $("#HsnCodeDropdown").select2();
            $("#filterType").select2();
        });
    </script>

    {{-- <style>
        .history-row td {
            /* color: #555; */
            color: rgb(0, 140, 255);
            opacity: 0.7;
        }
    </style> --}}

    <script>
    // This script handles the modal
    document.addEventListener("DOMContentLoaded", function() {
        const historyModal = new bootstrap.Modal(document.getElementById('sizeHistoryModal'));
        const loader = document.getElementById('sizeHistoryLoader');
        const content = document.getElementById('sizeHistoryContent');
        const tbody = document.getElementById('sizeHistoryTableBody');
        const itemInfoPlaceholder = document.getElementById('itemSizeInfoPlaceholder');

        // *** YEH HAI SAHI LINE ***
        // Hum 'item_sizes_report_table' ko target kar rahe hain
        $('#item_sizes_report_table tbody').on('click', '.view-size-history-btn', function(event) {
            event.preventDefault();
            const sizeId = this.getAttribute('data-size-id');
            const itemInfo = this.getAttribute('data-item-info');

            itemInfoPlaceholder.textContent = itemInfo;
            loader.style.display = 'block';
            content.style.display = 'none';
            tbody.innerHTML = '';

            fetch(`/reports/item-sizes/${sizeId}/history`)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach((historyItem, index) => {
                            let changeDate = new Date(historyItem.approval_time)
                                .toLocaleString('en-GB');
                                const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${changeDate}</td>
                                    <td>${historyItem.hsn_code || 'N/A'}</td>
                                    <td>${historyItem.rate != null ? Number(historyItem.rate).toLocaleString('en-IN', currencyFormat) : '₹0.00'}</td>
                                    <td>${historyItem.approved_by || 'N/A'}</td>
                                    <td>${historyItem.status || 'N/A'}</td>
                                    <td>${historyItem.remarks || 'N/A'}</td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    } else {
                        tbody.innerHTML =
                            '<tr><td colspan="7" class="text-center">No history found for this item size.</td></tr>';
                    }
                    loader.style.display = 'none';
                    content.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching size history:', error);
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
