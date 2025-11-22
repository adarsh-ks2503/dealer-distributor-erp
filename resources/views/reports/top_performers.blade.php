@extends('layouts.main')
@section('title', 'Top Performers Report - Singhal Steel')

@section('content')
<style>
    @media (max-width: 768px) {
        .dtfc-fixed-left,
        .dtfc-fixed-right {
            position: relative !important;
            transform: none !important;
            z-index: auto !important;
        }
    }

    .no-data-message {
        text-align: center;
        padding: 20px;
        color: #dc3545;
    }

    .order-table th,
    .order-table td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }

    .modal-content {
        widows: 10%;
    }

    .modal-dialog .modal-xl {
        widows: 10%;
    }

    /* New styles for team status */
    .team-header-active {
        background-color: #d4edda !important; /* Light green for active teams */
        color: #155724 !important; /* Dark green text for contrast */
        font-weight: bold;
    }

    .team-header-suspended {
        background-color: #f8d7da !important; /* Light red for suspended teams */
        color: #721c24 !important; /* Dark red text for contrast */
        font-weight: normal;
        opacity: 0.8; /* Subdued appearance */
    }
</style>
    <style>
        @media (max-width: 768px) {

            .dtfc-fixed-left,
            .dtfc-fixed-right {
                position: relative !important;
                transform: none !important;
                z-index: auto !important;
            }
        }

        .no-data-message {
            text-align: center;
            padding: 20px;
            color: #dc3545;
        }

        /* .modal-xl {
            max-width: 90%;
        } */

        .order-table th,
        .order-table td {
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        .modal-content {
            widows: 10%;
        }
        .modal-dialog .modal-xl {
            widows: 10%;
        }
    </style>

<main id="main" class="main">
    @include('partials.heading_cards.cards', [
        'heading' => 'Top Performers Report',
        'sub_heading' => 'Top Distributors and Dealers',
    ])

    <section class="section">
        <div class="card pt-4">
            <div class="card-body">
                {{-- Aapke saare filters ka HTML yahan hai --}}
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label for="timeSpan" class="mb-2"><strong>Time Span</strong></label>
                        <select class="custom-select form-control" id="timeSpan">
                            <option value="last_week">Last Week</option>
                            <option value="last_month" selected>Last Month</option>
                            <option value="last_quarter">Last Quarter</option>
                            <option value="last_year">Last Year</option>
                            <option value="ytd">Year to Date</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-sm-12 d-flex justify-content-end align-items-end">
                        <button class="btn btn-primary me-2" type="button" id="filterButton">Filter</button>
                        <button class="btn btn-secondary" type="button" id="resetButton">Reset</button>
                    </div>
                </div>

                <div class="row mt-3" id="customDateRange" style="display: none;">
                    <div class="col-md-3 col-sm-12">
                        <label for="filterFromdate" class="mb-2"><strong>From Date</strong></label>
                        <input type="date" class="form-control" id="filterFromdate"
                            value="{{ now()->subMonth()->toDateString() }}">
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <label for="filterTodate" class="mb-2"><strong>To Date</strong></label>
                        <input type="date" class="form-control" id="filterTodate"
                            value="{{ now()->toDateString() }}">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3 col-sm-12">
                        <label for="StateDropdown" class="mb-2"><strong>State</strong></label>
                        <select class="custom-select form-control" id="StateDropdown">
                            <option value="all" selected>All</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->state }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <label for="CityDropdown" class="mb-2"><strong>City</strong></label>
                        <select class="custom-select form-control" id="CityDropdown">
                            <option value="all" selected>All</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" data-state-id="{{ $city->state_id }}">
                                    {{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <label for="filterType" class="mb-2"><strong>Status</strong></label>
                        <select class="custom-select form-control" id="filterType">
                            <option value="all" selected>All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <label for="criteriaType" class="mb-2"><strong>Rank By</strong></label>
                        <select class="custom-select form-control" id="criteriaType">
                            <option value="dispatch" selected>Dispatch</option>
                            <option value="order">Order</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body pt-3">
                <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#distributors-tab-pane"
                            type="button" role="tab" aria-selected="true">Top Distributors</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dealers-tab-pane" type="button"
                            role="tab" aria-selected="false">Top Dealers</button>
                    </li>
                </ul>
                <div class="tab-content pt-2">
                    <div class="tab-pane fade show active" id="distributors-tab-pane" role="tabpanel">
                        <div id="distributorsNoData" class="no-data-message" style="display: none;">No distributors
                            found for the selected filters.</div>
                        <table id="top_distributors_table" class="display stripe row-border order-column" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text__left">RANK</th>
                                    <th class="text__left">NAME</th>
                                    <th class="text__left">CODE</th>
                                    <th class="text__left">STATUS</th>
                                    <th class="text__left">TOTAL QTY (MT)</th>
                                    <th class="text__left">TOTAL AMOUNT (₹)</th>
                                    <th class="text__left">VIEW DETAILS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="dealers-tab-pane" role="tabpanel">
                        <div id="dealersNoData" class="no-data-message" style="display: none;">No dealers found for
                            the selected filters.</div>
                        <table id="top_dealers_table" class="display stripe row-border order-column" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text__left">RANK</th>
                                    <th class="text__left">NAME</th>
                                    <th class="text__left">CODE</th>
                                    <th class="text__left">STATUS</th>
                                    <th class="text__left">TOTAL QTY (MT)</th>
                                    <th class="text__left">TOTAL AMOUNT (₹)</th>
                                    <th class="text__left">VIEW DETAILS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="dealerDetailsModal" tabindex="-1" aria-labelledby="dealerDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dealerDetailsModalLabel">Dealer Orders Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="dealerOrdersTableContainer">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="distributorDetailsModal" tabindex="-1"
        aria-labelledby="distributorDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="distributorDetailsModalLabel">Distributor Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#distributorOrdersTab" type="button" role="tab"
                                aria-selected="true">Orders/Dispatches</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#distributorTeamsTab"
                                type="button" role="tab" aria-selected="false">Teams</button>
                        </li>
                    </ul>
                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="distributorOrdersTab" role="tabpanel">
                            <div id="distributorOrdersTableContainer">
                                </div>
                        </div>
                        <div class="tab-pane fade" id="distributorTeamsTab" role="tabpanel">
                            <div id="distributorTeamsContainer">
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    // --- Global variables mein table instances ko store karein ---
    let distributorsTable;
    let dealersTable;

    // --- Sabhi functions ko pehle define karein ---
    function filterButton() {
        $.ajax({
            type: 'POST',
            url: '{{ route('get_top_performers.report') }}',
            data: {
                time_span: $('#timeSpan').val(),
                from_date: $('#timeSpan').val() === 'custom' ? $('#filterFromdate').val() : null,
                to_date: $('#timeSpan').val() === 'custom' ? $('#filterTodate').val() : null,
                state: $('#StateDropdown').val(),
                city: $('#CityDropdown').val(),
                status: $('#filterType').val(),
                criteria_type: $('#criteriaType').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                // Distributors Table Update
                distributorsTable.clear();
                $('#distributorsNoData').hide();
                if (res.distributors && res.distributors.length > 0) {
                    $('#top_distributors_table_wrapper').show(); // _wrapper ko show karein
                    res.distributors.forEach(function(item, index) {
                        const viewBtn = `<a href="#" class="btn btn-sm btn-primary view-distributor-details"
                            data-distributor-id="${item.id}"
                            data-distributor-name="${item.name}" data-distributor-code="${item.code}">View Details</a>`;
                            const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
                        
                        distributorsTable.row.add([
                            index + 1,
                            item.name ?? 'N/A',
                            item.code ?? 'N/A',
                            item.status ?? 'N/A',
                            item.total_qty ? Number(item.total_qty).toFixed(2) : '0.00',
                            item.total_amount != null ? Number(item.total_amount).toLocaleString('en-IN', currencyFormat) : '₹0.00',
                            viewBtn
                        ]);
                    });
                } else {
                    $('#distributorsNoData').show();
                    $('#top_distributors_table_wrapper').hide(); // _wrapper ko hide karein
                }
                distributorsTable.draw();

                // Dealers Table Update
                dealersTable.clear();
                $('#dealersNoData').hide();
                if (res.dealers && res.dealers.length > 0) {
                    $('#top_dealers_table_wrapper').show();
                    res.dealers.forEach(function(item, index) {
                        const dealerLink = `<a href="/dealers/show/${item.id}">${item.code ?? 'N/A'}</a>`;
                        const viewBtn = `<a href="#" class="btn btn-sm btn-primary view-dealer-details"
                                            data-dealer-id="${item.id}"
                                            data-dealer-name="${item.name}" data-dealer-code="${item.code}">View Details</a>`;
                                            const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
                        dealersTable.row.add([
                            index + 1,
                            item.name ?? 'N/A',
                            dealerLink,
                            item.status ?? 'N/A',
                            item.total_qty ? Number(item.total_qty).toFixed(2) : '0.00',
                            item.total_amount != null ? Number(item.total_amount).toLocaleString('en-IN', currencyFormat) : '₹0.00',

                            viewBtn
                        ]);
                    });
                } else {
                    $('#dealersNoData').show();
                    $('#top_dealers_table_wrapper').hide();
                }
                dealersTable.draw();

                if (res.error) {
                    alert(res.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", status, error, xhr.responseText);
            }
        });
    }

    function loadDealerDetails(dealerId, dealerName, dealerCode) {
        $('#dealerDetailsModalLabel').text(`Details - ${dealerName} (${dealerCode})`);
        const filters = {
            criteria_type: $('#criteriaType').val(),
            time_span: $('#timeSpan').val(),
            from_date: $('#timeSpan').val() === 'custom' ? $('#filterFromdate').val() : null,
            to_date: $('#timeSpan').val() === 'custom' ? $('#filterTodate').val() : null
        };
        $.ajax({
            url: `/reports/top-performers/dealer-orders/${dealerId}`,
            method: 'GET',
            data: filters,
            beforeSend: function() {
                $('#dealerOrdersTableContainer').html('<div class="text-center p-4"><div class="spinner-border" role="status"></div> Loading...</div>');
            },
            success: function(response) {
                if (response.success && response.orders) {
                    displayDealerOrders(response.orders);
                } else {
                    $('#dealerOrdersTableContainer').html('<div class="alert alert-danger">No orders found or invalid response</div>');
                }
            },
            error: function(xhr) {
                $('#dealerOrdersTableContainer').html('<div class="alert alert-danger">Error loading orders.</div>');
            }
        });
        $('#dealerDetailsModal').modal('show');
    }

    function loadDistributorDetails(distributorId, distributorName, distributorCode) {
        $('#distributorDetailsModalLabel').text(`Details - ${distributorName} (${distributorCode})`);
        const filters = {
            criteria_type: $('#criteriaType').val(),
            time_span: $('#timeSpan').val(),
            from_date: $('#timeSpan').val() === 'custom' ? $('#filterFromdate').val() : null,
            to_date: $('#timeSpan').val() === 'custom' ? $('#filterTodate').val() : null
        };
        $.when(
            $.ajax({
                url: `/reports/top-performers/distributor-orders/${distributorId}`,
                method: 'GET',
                data: filters
            }),
            $.ajax({
                url: `/reports/top-performers/distributor-teams/${distributorId}`,
                method: 'GET',
                data: filters 
            })
        ).done(function(ordersResponse, teamsResponse) {
            const ordersData = ordersResponse[0];
            const teamsData = teamsResponse[0];
            if (ordersData.success && ordersData.totals) {
                displayDistributorOrders(ordersData);
            } else {
                $('#distributorOrdersTableContainer').html('<div class="alert alert-danger">No orders/dispatches found</div>');
            }
            if (teamsData.success) {
                displayDistributorTeams(teamsData);
            } else {
                $('#distributorTeamsContainer').html('<div class="alert alert-info">No teams found</div>');
            }
        }).fail(function() {
            $('#distributorOrdersTableContainer').html('<div class="alert alert-danger">Error loading orders/dispatches.</div>');
            $('#distributorTeamsContainer').html('<div class="alert alert-danger">Error loading teams.</div>');
        });
        $('#distributorDetailsModal').modal('show');
    }

    function displayDealerOrders(orders) {
        let html = '';
        if (orders && orders.length > 0) {
            orders.forEach(order => {
                const isOrder = order.hasOwnProperty('order_number');
                const orderNumber = isOrder ? order.order_number : order.dispatch_number;
                const orderDate = isOrder ? order.order_date : order.dispatch_date;
                const grandTotal = order.grand_total || order.total_amount || 0;
                let orderNumberHtml = orderNumber;
                if (isOrder && order.placed_by_distributor_id) {
                    orderNumberHtml += ' <span class="badge bg-secondary" title="Placed by Distributor">D</span>';
                }

                html += `<div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between flex-wrap">
                                <span><strong>${isOrder ? 'Order' : 'Dispatch'} #:</strong> ${orderNumberHtml}</span>
                                <span><strong>Date:</strong> ${moment(orderDate).format('DD MMM, YYYY')}</span>
                                <span class="fw-bold">Grand Total: ₹${parseFloat(grandTotal).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-end">Qty</th>
                                            <th class="text-end">Price (₹)</th>
                                            <th class="text-end">Total (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                order.items.forEach(item => {
                    const qty = isOrder ? (item.qty || 0) : (item.dispatch_qty || 0);
                    const price = isOrder ? (item.agreed_basic_price || 0) : (item.final_price || 0);
                    const itemTotal = item.item_total || 0;
                    const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
                    html += `<tr>
                                <td>${isOrder ? 'Order Item' : item.item_name || 'N/A'}</td>
                                <td class="text-end">${parseFloat(qty).toLocaleString('en-IN')}</td>
                                <td class="text-end">${parseFloat(price).toLocaleString('en-IN', currencyFormat)}</td>
<td class="text-end">${parseFloat(itemTotal).toLocaleString('en-IN', currencyFormat)}</td>
                            </tr>`;
                });
                if (isOrder) {
                    const charges = (order.loading_charge || 0) + (order.insurance_charge || 0);
                    if (charges > 0) {
                        html += `<tr>
                                    <td colspan="3" class="text-end"><em>Loading + Insurance</em></td>
                                    <td class="text-end"><em>${charges.toLocaleString('en-IN', {minimumFractionDigits: 2})}</em></td>
                                </tr>`;
                    }
                }
                html += `</tbody></table></div></div>`;
            });
        } else {
            html = '<div class="alert alert-secondary">No records found for this dealer in the selected period.</div>';
        }
        $('#dealerOrdersTableContainer').html(html);
    }

    function displayDistributorOrders(ordersData) {
        let html = '';
        if (ordersData && ordersData.success && ordersData.totals) {
            const totals = ordersData.totals;
            const numberFormat = { minimumFractionDigits: 2, maximumFractionDigits: 2 };
const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };
            html = `<table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Total Orders / Dispatches</th>
                                <th>Total Qty (MT)</th>
                                <th>Total Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${totals.total_orders}</td>
                                <td>${Number(totals.total_qty).toLocaleString('en-IN', numberFormat)}</td>
    <td>${Number(totals.total_amount).toLocaleString('en-IN', currencyFormat)}</td>
                            </tr>
                        </tbody>
                    </table>`;
        } else {
            html = '<div class="alert alert-danger">Error: Unable to display totals. Invalid data format.</div>';
        }
        $('#distributorOrdersTableContainer').html(html);
    }

    function displayDistributorTeams(teamsData) {
        let html = '';
        const teams = teamsData.teams;
        const distributor = teamsData.distributor;
        if ((teams && teams.length > 0) || distributor) {
            html += `<table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Individual Qty</th>
                                <th>Total Orders</th>
                            </tr>
                        </thead>
                        <tbody>`;
            if (distributor) {
                html += `<tr class="table-info">
                            <td><strong>${distributor.name || 'N/A'} (Direct)</strong></td>
                            <td>${distributor.code || 'N/A'}</td>
                            <td>${distributor.individual_qty ? parseFloat(distributor.individual_qty).toLocaleString() : '0'}</td>
                            <td>${distributor.total_orders || '0'}</td>
                        </tr>`;
            }
            if(teams && teams.length > 0) {
                const sortedTeams = [...teams].sort((a, b) => {
                    const statusA = (a.status || '').toLowerCase();
                    const statusB = (b.status || '').toLowerCase();
                    if (statusA === 'active' && statusB !== 'active') return -1;
                    if (statusA !== 'active' && statusB === 'active') return 1;
                    return 0;
                });
                sortedTeams.forEach(team => {
                    const teamHeaderClass = team.status && team.status.toLowerCase() === 'active' ? 'team-header-active' : 'team-header-suspended';
                    html += `<tr class="${teamHeaderClass}">
                                <td colspan="4">
                                    <strong>Team ID: ${team.id}</strong> | 
                                    Status: ${team.status || 'N/A'} | 
                                    Total Limit: ${team.total_order_limit ? parseFloat(team.total_order_limit).toLocaleString() : 'N/A'}
                                </td>
                            </tr>`;
                    if (team.dealers && team.dealers.length > 0) {
                        team.dealers.forEach(dealer => {
                            html += `<tr>
                                        <td>${dealer.name || 'N/A'}</td>
                                        <td>${dealer.code || 'N/A'}</td>
                                        <td>${dealer.individual_qty ? parseFloat(dealer.individual_qty).toLocaleString() : '0'}</td>
                                        <td>${dealer.total_orders || '0'}</td>
                                    </tr>`;
                        });
                    } else {
                        html += `<tr><td colspan="4" class="text-center">No dealers found in this team.</td></tr>`;
                    }
                });
            }
            html += `</tbody></table>`;
        } else {
            html = '<div class="alert alert-info">No teams found for this distributor.</div>';
        }
        $('#distributorTeamsContainer').html(html);
    }

    function displayDistributorContacts(contacts) {
        // ... (Yeh function pehle jaisa hi hai)
    }

    // --- YAHAN SE SCRIPT KA MAIN HISSA SHURU HOTA HAI ---
    
    // Sirf ek $(document).ready() block
    $(document).ready(function() {
        
        // --- DataTables ke advanced options (buttons ke saath) ---
        let options = {
            responsive: true,
            paging: true,
            scrollCollapse: false,
            scrollY: 400,
            lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            layout: {
                topStart: {
                    buttons: [
                        'pageLength',
                        {
                            extend: "excel", text: "Excel", title: "Top Performers Report",
                            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } // Column 6 (View Details) ko skip karein
                        },
                        {
                            extend: "pdfHtml5", text: "PDF", title: "Top Performers Report",
                            orientation: "landscape", pageSize: "A4",
                            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] }
                        },
                        // {
                        //     extend: 'print', text: 'Print', // <-- PRINT BUTTON
                        //     exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] }
                        // }
                    ]
                }
            },
            "columnDefs": [ { "targets": 6, "orderable": false } ] // Column 6 (View Details) ko sorting se hatayein
        };

        // --- Tables ko SIRF EK BAAR initialize karein ---
        distributorsTable = new DataTable("#top_distributors_table", options);
        dealersTable = new DataTable("#top_dealers_table", options);

        // --- Baaki ke event listeners ---
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        $("#timeSpan, #StateDropdown, #CityDropdown, #filterType, #criteriaType").select2();

        $('#timeSpan').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#customDateRange').show();
            } else {
                $('#customDateRange').hide();
                $('#filterFromdate').val('');
                $('#filterTodate').val('');
            }
        });

        const allCities = @json($cities);
        $('#StateDropdown').on('change', function() {
            const selectedStateId = $(this).val();
            const cityDropdown = $('#CityDropdown');
            cityDropdown.empty().append('<option value="all" selected>All</option>');
            if (selectedStateId && selectedStateId !== 'all') {
                const filteredCities = allCities.filter(city => city.state_id == selectedStateId);
                filteredCities.forEach(city => {
                    cityDropdown.append(
                        `<option value="${city.id}" data-state-id="${city.state_id}">${city.name}</option>`
                    );
                });
            }
            cityDropdown.trigger('change');
        });

        filterButton(); // Initial data load

        $('#filterButton').click(filterButton);
        $('#resetButton').click(function() {
            $('#timeSpan').val('last_month').trigger('change');
            $('#StateDropdown').val('all').trigger('change');
            $('#filterType').val('all').trigger('change');
            $('#criteriaType').val('dispatch').trigger('change');
            $('#filterFromdate').val('{{ now()->subMonth()->toDateString() }}');
            $('#filterTodate').val('{{ now()->toDateString() }}');
            filterButton();
        });

        // Dealer modal click handler
        $('#top_dealers_table').on('click', '.view-dealer-details', function(e) {
            e.preventDefault();
            const dealerId = $(this).data('dealer-id');
            const dealerName = $(this).data('dealer-name');
            const dealerCode = $(this).data('dealer-code');
            loadDealerDetails(dealerId, dealerName, dealerCode);
        });

        // Distributor modal click handler
        $('#top_distributors_table').on('click', '.view-distributor-details', function(e) {
            e.preventDefault();
            const distributorId = $(this).data('distributor-id');
            const distributorName = $(this).data('distributor-name');
            const distributorCode = $(this).data('distributor-code');
            loadDistributorDetails(distributorId, distributorName, distributorCode);
        });
    });
</script>
@endsection