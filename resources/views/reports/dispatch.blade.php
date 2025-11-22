@extends('layouts.main')
@section('title', 'Dispatch Report - Singhal')
@section('content')
    <main id="main" class="main">
        @include('partials.heading_cards.cards', [
            'heading' => 'Dispatch Report',
            'sub_heading' => 'Dispatch Report',
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
                $('#DealersNameDropdown').val(),
                $('#DistributorsNameDropdown').val(),
                $('#dispatchNumberDropdown').val(),
                $('#filterWarehouse').val(),
                $('#filterType').val()
            )">
                                    Filter
                                </button>
                                <button class="mr-2 btn btn-primary" type="button" id="resetButton">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="page-header">
                        <div class="row">
                            @include('partials.heading_cards.date', ['fromValue' => $fromValue ?? '', 'toValue' => $toValue ?? ''])
                            <div class="col-md-2 col-sm-12 mt-3">
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
                                <label for="" class="mb-2"><strong>Dispatch Number</strong></label>
                                <select class="custom-select form-control" name="item_name" id="dispatchNumberDropdown">
                                    <option value="" disabled>Select Dispatch Number</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($dispatchs as $item)
                                        <option value="{{ $item->dispatch_number }}"
                                            @if ($item->id == $selectedId) selected @endif>{{ $item->dispatch_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="filterWarehouse" class="mb-2"><strong>Warehouse</strong></label>
                                <select class="form-select form-control" name="warehouse_id" id="filterWarehouse">
                                    <option value="" disabled>Select Warehouse</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Status</strong></label>
                                <select class="custom-select form-control" name="item_name" id="filterType">
                                    <option value="" disabled>Select Status</option>
                                    <option value="all" {{ (!isset($type) || $type == 'all') ? 'selected' : '' }}>All</option>
                                    <option value="pending" {{ $type == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $type == 'approved' ? 'selected' : '' }}>Approved</option>
                                    {{-- <option value="cancelled">Cancelled</option> --}}
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
                            <table id="dispatch_report_table" class="display stripe row-border order-column"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text__left">S. NO</th>
                                        <th class="text__left">DISPATCH ID</th>
                                        <th class="text__left">DEALER <br> /DISTRIBUTOR NAME</th>
                                        <th class="text__left">TYPE</th>
                                        <th class="text__left">DISPATCH DATE</th>
                                        <th class="text__left">TOTAL ITEMS</th>
                                        <th class="text__left">DISPATCHED QTY</th>
                                        <th class="text__left">TOTAL AMOUNT</th>
                                        <th class="text__left">VEHICLE NO</th>
                                        <th class="text__left">DRIVER NAME</th>
                                        <th class="text__left">WAREHOUSE NAME</th>
                                        <th class="text__left">STATUS</th>
                                        {{-- <th>Action</th> --}}
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
        <!-- Dispatch Items Modal -->
        <div class="modal fade" id="dispatchItemsModal" tabindex="-1" aria-labelledby="dispatchItemsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dispatch Items - <span id="dispatchNumberPlaceholder"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="overflow-x: auto;">
                        <div id="itemsLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading items...</p>
                        </div>
                        <div id="itemsContent" style="display: none;">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-right">#</th>
                                        <th class="text-right">Order Number</th>
                                        <th class="text-right">Item Name</th>
                                        <th class="text-right">Size (mm)</th>
                                        <th class="text-right">Dispatch Qty (MT)</th>
                                        <th class="text-right">Basic Price (₹/MT)</th>
                                        <th class="text-right">Gauge Diff (₹)</th>
                                        <th class="text-right">Final Price (₹/MT)</th>
                                        <th class="text-right">GST (%)</th>
                                        <th class="text-right">Total Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Data will be populated here -->
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
        function filterButton(filterFromdate, filterTodate, DealersNameDropdown, DistributorsNameDropdown,
            dispatchNumberDropdown, filterWarehouse,
            filterType) {
            $.ajax({
                type: 'POST',
                url: '{{ route('get_dispatch.report') }}',
                data: {
                    from_date: filterFromdate,
                    to_date: filterTodate,
                    dealer_name: DealersNameDropdown,
                    distributer_name: DistributorsNameDropdown,
                    dispatch_number: dispatchNumberDropdown,
                    warehouse_id: filterWarehouse,
                    type: filterType,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    var table = $('#dispatch_report_table').DataTable();
                    table.clear().draw();
                    let rows = [];
                    res.forEach(function(item, index) {
                        console.log(item);
                        let dt = new Date(item.dispatch_date);
                        let formattedDate = dt.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }).replace(/ /g, '-');
                        let time = dt.toLocaleTimeString('en-GB', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        }).toLowerCase();
                        let fullFormatted = `${formattedDate} `;
                        let modalId = `saudaModal_${item.id}`;
                        // =============================================================
                        // --- THE FIX IS HERE ---
                        // =============================================================
                        // let formattedOutTime = 'N/A'; // Default value
                        // // Check if dispatch_out_time exists and is not null
                        // if (item.dispatch_out_time) {
                        //     // Create a temporary date object from a dummy date + your time string
                        //     const tempTime = new Date(`1970-01-01T${item.dispatch_out_time}`);
                        //     // Format it to 12-hour format with am/pm
                        //     formattedOutTime = tempTime.toLocaleTimeString('en-US', {
                        //         hour: '2-digit',
                        //         minute: '2-digit',
                        //         hour12: true
                        //     });
                        // }
                        // =============================================================
                        let dispatchLink =
                            `<a href="{{ route('dispatch.show', '') }}/${item.id}">${item.dispatch_number ?? 'N/A'}</a>`;
                        const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
                        if (Array.isArray(item.dispatch_items) && item.dispatch_items.length > 0) {
                            // 3. reduce() function ka istemal karke sabhi dispatch_qty ko jodein
                            totalDispatchedQty = item.dispatch_items.reduce((sum, currentItem) => {
                                // parseFloat() ka istemal zaroori hai taaki string ("2.00") number (2.00) ban jaye
                                return sum + parseFloat(currentItem.dispatch_qty);
                            }, 0); // 0 se jodna shuru karein
                        }
                        let totalItemsCell = `<span class="view-dispatch-items-btn"
                             data-dispatch-id="${item.id}"
                             data-dispatch-number="${item.dispatch_number}"
                             style="color: #0d6efd; cursor: pointer;">
                             ${Array.isArray(item.dispatch_items) ? item.dispatch_items.length : '0'}
                          </span>`;
                        rows.push([
                            // Dispatch ID
                            index + 1,
                            // item.dispatch_number ?? 'N/A',
                            dispatchLink,
                            // Dealer/Distributor Name
                            item.dealer ? item.dealer.name : (item.distributor ? item
                                .distributor.name : 'N/A'),
                            // Type (Dealer or Distributor)
                            item.dealer ? 'Dealer' : (item.distributor ? 'Distributor' : 'N/A'),
                            // Dispatch Date
                            // item.dispatch_date ?? 'N/A',
                            fullFormatted,
                            totalItemsCell,
                            // Dispatch Time
                            // item.dispatch_time ?? 'N/A',
                            // item.dispatch_out_time ?? 'N/A',
                            // formattedOutTime,
                            // Dispatched Qty
                            // item.dispatched_quantity ?? '0',
                            // item.dispatch_items[0].dispatch_qty ?? '0',
                            totalDispatchedQty.toFixed(2) + ' MT',
                            // item.total_amount ?? 'N/A',
                            item.total_amount != null ? Number(item.total_amount).toLocaleString('en-IN', currencyFormat) : 'N/A',
                            // Total Items
                            // Array.isArray(item.dispatch_items) ? item.dispatch_items.length :
                            // '0',
                            // Vehicle No
                            item.vehicle_no ?? 'N/A',
                            // Driver Name
                            item.driver_name ?? 'N/A',
                            // Warehouse
                            item.warehouse ? item.warehouse.name : 'N/A',
                            //item.loading_point_id ?? 'N/A',
                            // Status
                            // item.status ?? 'N/A',
                            item.status ? item.status.charAt(0).toUpperCase() + item.status
                            .slice(1) : 'Pending',
                            // Action (View/Edit buttons)
                            // `<a href="/dispatch/show/${item.id}" class="btn btn-sm btn-primary">View</a>`
                        ]);
                        // $(newRow).find(
                        // 'td:eq(0), td:eq(1), td:eq(2), td:eq(3), td:eq(4),td:eq(5),td:eq(6),td:eq(7),td:eq(8), td:eq(9), td:eq(10), td:eq(11)'
                        // ).addClass(
                        // 'text__left');
                    });
                    // Add all rows in one go
                    table.rows.add(rows).draw();
                    // Add class to specific cells after draw
                    // $('#dispatch_items_report_table tbody tr').each(function() {
                    // $(this).find(
                    // 'td:eq(0), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11), td:eq(11), td:eq(12), td:eq(13)'
                    // ).addClass(
                    // 'text__left');
                    // });
                },
                // error: function(xhr, status, error) {
                // console.error("AJAX request failed:", status, error);
                // }
            });
        }
        $(document).ready(function() {
            // Parse URL params for dates and type
            const urlParams = new URLSearchParams(window.location.search);
            let initialFrom = urlParams.get('from_date') || '{{ $fromValue ?? "" }}';
            let initialTo = urlParams.get('to_date') || '{{ $toValue ?? "" }}';
            let initialType = urlParams.get('type') || 'all';
            // Set dates and type
            $('#filterFromdate').val(initialFrom);
            $('#filterTodate').val(initialTo);
            $('#filterType').val(initialType);
            // Call filterButton when document is ready
            filterButton(
                initialFrom,
                initialTo,
                $('#DealersNameDropdown').val(),
                $('#DistributorsNameDropdown').val(),
                $('#dispatchNumberDropdown').val(),
                $('#filterWarehouse').val(),
                initialType
            );
            // Filter button onClick handler
            $('#filterButton').click(function() {
                filterButton(
                    $('#filterFromdate').val(),
                    $('#filterTodate').val(),
                    $('#DealersNameDropdown').val(),
                    $('#DistributorsNameDropdown').val(),
                    $('#dispatchNumberDropdown').val(),
                    $('#filterWarehouse').val(),
                    $('#filterType').val()
                );
            });
            // Reset button functionality - reset to default (FY or current month based on access)
            $('#resetButton').click(function() {
                $('#filterFromdate').val('{{ $fromValue ?? "" }}');
                $('#filterTodate').val('{{ $toValue ?? "" }}');
                $('#filterType').val('all').trigger('change');
                $('#DealersNameDropdown').val('all').trigger('change');
                $('#DistributorsNameDropdown').val('all').trigger('change');
                $('#dispatchNumberDropdown').val('all').trigger('change');
                $('#filterWarehouse').val('all').trigger('change');
                filterButton(
                    '{{ $fromValue ?? "" }}',
                    '{{ $toValue ?? "" }}',
                    'all',
                    'all',
                    'all',
                    'all',
                    'all'
                );
            });
            // Select2 initialization
            $("#dispatchNumberDropdown").select2();
            $("#filterWarehouse").select2();
            $("#statusDropdown").select2();
            $("#filterType").select2();
            $("#DistributorsNameDropdown").select2();
            $("#DealersNameDropdown").select2();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const itemsModal = new bootstrap.Modal(document.getElementById('dispatchItemsModal'));
            const loader = document.getElementById('itemsLoader');
            const content = document.getElementById('itemsContent');
            const tbody = document.getElementById('itemsTableBody');
            const dispatchNumberPlaceholder = document.getElementById('dispatchNumberPlaceholder');
            $('#dispatch_report_table tbody').on('click', '.view-dispatch-items-btn', function(event) {
                event.preventDefault();
                const dispatchId = this.getAttribute('data-dispatch-id');
                const dispatchNumber = this.getAttribute('data-dispatch-number');
                dispatchNumberPlaceholder.textContent = dispatchNumber;
                loader.style.display = 'block';
                content.style.display = 'none';
                tbody.innerHTML = '';
                // Replace your existing fetch block with this one
                fetch(`/reports/dispatch/${dispatchId}/items`)
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach((item, index) => {
                                const numberFormat = { minimumFractionDigits: 2, maximumFractionDigits: 2 };
    const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
                                const row = `
                    <tr>
                        <td>${index + 1}</td>
            <td>${item.order?.order_number || 'N/A'}</td>
            <td>${item.item?.name || 'TMT Bar'}</td>
            <td>${item.size?.size || 'N/A'}</td>
            <td class="text-right">${parseFloat(item.dispatch_qty).toLocaleString('en-IN', numberFormat)}</td>
            <td class="text-right">${parseFloat(item.basic_price).toLocaleString('en-IN', currencyFormat)}</td>
            <td class="text-right">${parseFloat(item.gauge_diff).toLocaleString('en-IN', currencyFormat)}</td>
            <td class="text-right">${parseFloat(item.final_price).toLocaleString('en-IN', currencyFormat)}</td>
            <td class="text-right">${parseFloat(item.gst).toLocaleString('en-IN', numberFormat)}</td>
            <td class="text-right">${parseFloat(item.total_amount).toLocaleString('en-IN', currencyFormat)}</td>
                    </tr>
                `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            // Update colspan to 11
                            tbody.innerHTML =
                                '<tr><td colspan="11" class="text-center">No items found for this dispatch.</td></tr>';
                        }
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching dispatch items:', error);
                        tbody.innerHTML =
                            '<tr><td colspan="11" class="text-center text-danger">Failed to load items.</td></tr>';
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    });
                itemsModal.show();
            });
        });
    </script>
    @push('styles')
        <style>
            @media (min-width: 1200px) {
                #dispatchItemsModal .modal-dialog {
                    max-width: 80%;
                }
            }
            #dispatchItemsModal .table {
                font-size: 15px;
                /* Slightly smaller font */
            }
            #dispatchItemsModal .table th,
            #dispatchItemsModal .table td {
                padding: 8px 6px;
                white-space: nowrap;
            }
            #dispatchItemsModal .modal-body {
                overflow-x: auto;
            }
        </style>
    @endpush
@endsection
