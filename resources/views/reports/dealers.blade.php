@extends('layouts.main')
@section('title', 'Dealers Report - Singhal Steel')
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
            'heading' => 'Dealers Report',
            'sub_heading' => 'Dealers Report',
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
                $('#StateDropdown').val(),
                $('#CityDropdown').val(),
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
                            @include('partials.heading_cards.date')


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
                                <label for="" class="mb-2"><strong>Assig. Distributor</strong></label>
                                <select class="custom-select form-control" id="DistributorsNameDropdown">
                                    <option value="" disabled>Select Assigned Distributor</option>
                                    <option value="all" selected>All</option>

                                    @foreach ($distributors as $distributor)
                                        <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>State</strong></label>
                                <select class="custom-select form-control" id="StateDropdown">
                                    <option value="" disabled>Select State</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>City</strong></label>
                                <select class="custom-select form-control" id="CityDropdown">
                                    <option value="" disabled>Select City</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($city as $cty)
                                        <option value="{{ $cty->id }}">{{ $cty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Status</strong></label>
                                <select class="custom-select form-control" name="item_name" id="filterType">
                                    <option value="" disabled>Select Status</option>
                                    <option value="all" selected>All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
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
                            <table id="dealers_report_table" class="display stripe row-border order-column"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S. NO</th>
                                        {{-- <th>Dealer ID</th> --}}
                                        <th>NAME</th>
                                        <th>TYPE</th>
                                        <th>CODE</th>
                                        <th>ASSIGNED <br> DISTRIBUTOR</th>
                                        <th>ORDER LIMIT</th>
                                        <th>ALLOWED <br> ORDER LIMIT</th>
                                        <th>STATE</th>
                                        <th>CITY</th>
                                        <th>MOBILE NO</th>
                                        <th>TOTAL CONTACT <br> PERSON</th>
                                        <th>STATUS</th>
                                        <th>CREATED AT</th>
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



        <div class="modal fade" id="orderLimitHistoryModal" tabindex="-1" aria-labelledby="orderLimitHistoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Limit History - <span id="dealerNamePlaceholder"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="historyLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading history...</p>
                        </div>
                        <div id="historyContent" style="display: none;">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date of Change</th>
                                        <th>Previous Limit (₹)</th>
                                        <th>New Limit (₹)</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="contactPersonsModal" tabindex="-1" aria-labelledby="contactPersonsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Contact Persons - <span id="contactDealerNamePlaceholder"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="contactsLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading contacts...</p>
                        </div>
                        <div id="contactsContent" style="display: none;">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Mobile No.</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody id="contactsTableBody">
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
            StateDropdown, CityDropdown, filterType) {
            $.ajax({
                type: 'POST',
                url: '{{ route('get_dealers.report') }}',
                data: {
                    from_date: filterFromdate,
                    to_date: filterTodate,
                    dealer_name: DealersNameDropdown,
                    distributer_name: DistributorsNameDropdown,
                    state: StateDropdown,
                    city: CityDropdown,
                    type: filterType,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    var table = $('#dealers_report_table').DataTable();
                    table.clear().draw();
                    let rows = [];

                    res.forEach(function(item, index) {
                        console.log(item);
                        let dt = new Date(item.created_at);
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
                        // Default values
                        let fsrBasic = '0';
                        let lightChannelBasic = '0';
                        let heavyChannelBasic = '0';


                        let agreedPrice = parseFloat(item.agreed_basic_price ?? 0);
                        let insurance = parseFloat(item.insurance_charge ?? 0);
                        let loading = parseFloat(item.loading_charge ?? 0);

                        let totalAmount = agreedPrice + insurance + loading;
                        const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };

                        let dealerLink =
                            `<a href="{{ route('dealers.show', '') }}/${item.id}">${item.code ?? 'N/A'}</a>`;

                    //     let orderLimitCell = `<span title="View Order Limit History" class="view-limit-history-btn" 
                    //     data-dealer-id="${item.id}" 
                    //     data-dealer-name="${item.name}"
                    //     style="color: #0d6efd; cursor: pointer;">
                    //     ${item.order_limit ?? '0'}
                    //   </span>`;

                    // Pehle value ko format karein
const numberFormat = { 
    style: 'decimal', 
    minimumFractionDigits: 2, 
    maximumFractionDigits: 2 
};

// 2. Pehle value ko format karein (bina ₹ symbol ke)
const formattedLimit = item.order_limit != null 
    ? Number(item.order_limit).toLocaleString('en-IN', numberFormat) 
    : '0.00';

// 3. Ab formatted value ko HTML string mein daalein aur ' MT' add karein
let orderLimitCell = `<span title="View Order Limit History" class="view-limit-history-btn" 
                        data-dealer-id="${item.id}" 
                        data-dealer-name="${item.name}"
                        style="color: #0d6efd; cursor: pointer;">
                        ${formattedLimit} MT
                    </span>`; // <-- ' MT' yahan add kiya

                        // This makes the "Name" column clickable for contact persons
                        let dealerContactPersons = `<span title="View Contact Person List" class="view-contacts-btn"
                            data-dealer-id="${item.id}"
                            data-dealer-name="${item.name}"
                            style="color: #0d6efd; cursor: pointer;">
                            ${item.contact_persons.length}
                      </span>`;

                        rows.push([
                            index + 1,
                            // item.id ?? 'N/A',
                            item.name ?? 'N/A',
                            // dealerNameCell,
                            item.type ?? 'N/A',
                            // item.code ?? 'N/A',
                            dealerLink,
                            // item.distributor_id ?? 'N/A',
                            item.distributor?.name ?? 'N/A',
                            // item.order_limit ?? '0',
                            orderLimitCell,
                            // item.allowed_order_limit ?? '0',
                            item.allowed_order_limit + ' MT',
                            item.state.state ?? 'N/A',
                            item.city.name ?? 'N/A',
                            item.mobile_no ?? 'N/A',
                            dealerContactPersons,
                            item.status ?? 'N/A',
                            fullFormatted,
                        ]);


                        // $(newRow).find(
                        //     'td:eq(0), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11)'
                        // ).addClass(
                        //     'text__left');
                    });
                    // Add all rows in one go
                    table.rows.add(rows).draw();

                    // Add class to specific cells after draw
                    // $('#dealers_report_table tbody tr').each(function() {
                    //     $(this).find(
                    //         'td:eq(0), td:eq(4), td:eq(5), td:eq(6), td:eq(7), td:eq(8), td:eq(9), td:eq(10), td:eq(11), td:eq(11), td:eq(12), td:eq(13)'
                    //     ).addClass(
                    //         'text__left');
                    // });
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
                $('#DealerNameDropdown').val(),
                $('#DistributorsNameDropdown').val(),
                $('#StateDropdown').val(),
                $('#CityDropdown').val(),
                $('#filterType').val()
            );

            // Filter button onClick handler
            $('#filterButton').click(function() {
                filterButton(
                    $('#filterFromdate').val(),
                    $('#filterTodate').val(),
                    $('#DealerNameDropdown').val(),
                    $('#DistributorsNameDropdown').val(),
                    $('#StateDropdown').val(),
                    $('#CityDropdown').val(),
                    $('#filterType').val()
                );
            });

            // Reset button functionality
            $('#resetButton').click(function() {
                location.reload();
            });

            // Select2 initialization
            $("#DealersNameDropdown").select2();
            $("#DistributorsNameDropdown").select2();
            $("#StateDropdown").select2();
            $("#CityDropdown").select2();
            $("#filterType").select2();
        });
    </script>


    <script>
        const allCities = @json($city);

        $(document).ready(function() {
            // Step 2: Listen for a change in the State dropdown
            $('#StateDropdown').on('change', function() {
                // Get the ID of the selected state
                const selectedStateId = $(this).val();
                const cityDropdown = $('#CityDropdown');

                // Step 3: Clear all previous options from the City dropdown
                cityDropdown.empty();
                // Add the default "All" option back
                cityDropdown.append('<option value="all" selected>All</option>');

                // If a specific state is selected (not "All")
                if (selectedStateId && selectedStateId !== 'all') {

                    // Step 4: Filter the 'allCities' array.
                    // This will create a new array containing only the cities that match the selected state ID.
                    const filteredCities = allCities.filter(city => city.state_id == selectedStateId);

                    // Step 5: Add the filtered cities to the dropdown
                    if (filteredCities.length > 0) {
                        filteredCities.forEach(function(city) {
                            cityDropdown.append('<option value="' + city.id + '">' + city.name +
                                '</option>');
                        });
                    }
                }

                // If you are using a library like Select2, you might need to refresh it
                // cityDropdown.select2();
            });
        });
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const historyModal = new bootstrap.Modal(document.getElementById('orderLimitHistoryModal'));
            const loader = document.getElementById('historyLoader');
            const content = document.getElementById('historyContent');
            const tbody = document.getElementById('historyTableBody');
            const dealerNamePlaceholder = document.getElementById('dealerNamePlaceholder');

            // Use event delegation for dynamically added buttons
            $('#dealers_report_table tbody').on('click', '.view-limit-history-btn', function() {
                event.preventDefault();
                const dealerId = this.getAttribute('data-dealer-id');
                const dealerName = this.getAttribute('data-dealer-name');

                // Prepare the modal
                dealerNamePlaceholder.textContent = dealerName;
                loader.style.display = 'block';
                content.style.display = 'none';
                tbody.innerHTML = ''; // Clear previous results

                // Fetch the history data
                fetch(`/reports/dealers/${dealerId}/order-limit-history`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach((historyItem, index) => {
                                let changeDate = new Date(historyItem.updated_at)
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
                                <td>${historyItem.order_limit + ' MT'}</td>
    <td>${historyItem.desired_order_limit + ' MT'}</td>
                                <td>${historyItem.remarks || 'N/A'}</td>
                                <td>${historyItem.status || 'N/A'}</td>
                            </tr>
                        `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="6" class="text-center">No order limit history found.</td></tr>';
                        }

                        // Show the content
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching history:', error);
                        tbody.innerHTML =
                            '<tr><td colspan="6" class="text-center text-danger">Failed to load history.</td></tr>';
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    });

                historyModal.show();
            });
        });
    </script>





    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const contactModal = new bootstrap.Modal(document.getElementById('contactPersonsModal'));
            const loader = document.getElementById('contactsLoader');
            const content = document.getElementById('contactsContent');
            const tbody = document.getElementById('contactsTableBody');
            const dealerNamePlaceholder = document.getElementById('contactDealerNamePlaceholder');

            // Use event delegation for the new '.view-contacts-btn' class
            $('#dealers_report_table tbody').on('click', '.view-contacts-btn', function(event) {
                event.preventDefault(); // Stop the page from jumping
                const dealerId = this.getAttribute('data-dealer-id');
                const dealerName = this.getAttribute('data-dealer-name');

                // Prepare the modal
                dealerNamePlaceholder.textContent = dealerName;
                loader.style.display = 'block';
                content.style.display = 'none';
                tbody.innerHTML = ''; // Clear previous results

                // Fetch the contact persons data
                fetch(`/reports/dealers/${dealerId}/contact-persons`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach((contact, index) => {
                                const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${contact.name || 'N/A'}</td>
                                <td>${contact.mobile_no || 'N/A'}</td>
                                <td>${contact.email || 'N/A'}</td>
                            </tr>
                        `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="4" class="text-center">No contact persons found for this dealer.</td></tr>';
                        }

                        // Show the content
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching contacts:', error);
                        tbody.innerHTML =
                            '<tr><td colspan="4" class="text-center text-danger">Failed to load contact persons.</td></tr>';
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    });

                contactModal.show();
            });
        });
    </script>




    @push('styles')
        <style>
            .view-line-items-btn {
                font-size: 14px;
                font-weight: 500;
                border-radius: 20px;
                padding: 6px 14px;
                transition: all 0.2s ease-in-out;
            }

            .view-line-items-btn:hover {
                background-color: #0ea5e9;
                color: white;
                transform: scale(1.05);
            }

            .order-table td,
            .order-table th {
                text-align: center !important;
            }

            .order-table thead tr {
                background: linear-gradient(to right, #4f46e5, #6366f1);
                color: white;
            }

            .order-table thead th {
                padding: 14px 10px;
                text-transform: uppercase;
                font-size: 14px;
                border: none;
            }

            .order-table tbody tr {
                background: #f9fafb;
                transition: all 0.2s ease;
            }

            .order-table tbody tr:hover {
                background: #e0f2fe;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            }

            .order-table td {
                padding: 12px 10px;
                vertical-align: middle;
                font-size: 15px;
                border: none;
            }

            .action-btn {
                background: #6366f1;
                color: white;
                border-radius: 50%;
                width: 38px;
                height: 38px;
                display: flex;
                justify-content: center;
                align-items: center;
                border: none;
                transition: 0.3s;
            }

            .action-btn:hover {
                background: #4f46e5;
                transform: scale(1.1);
            }

            .dropdown-menu.animated-dropdown {
                animation: fadeInScale 0.25s ease-in-out;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                padding: 8px 0;
                min-width: 180px;
            }

            .dropdown-menu.animated-dropdown .dropdown-item {
                display: flex;
                align-items: center;
                font-size: 15px;
                padding: 10px 18px;
                color: #333;
                transition: 0.2s ease-in-out;
            }

            .dropdown-menu.animated-dropdown .dropdown-item:hover {
                background: #f1f5f9;
                transform: translateX(5px);
            }

            @keyframes fadeInScale {
                0% {
                    opacity: 0;
                    transform: scale(0.95) translateY(-10px);
                }

                100% {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }

            .custom-btn-primary {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
                color: #fff;
                border: none;
                padding: 10px 24px;
                border-radius: 8px;
                font-weight: 500;
                transition: all 0.3s ease-in-out;
            }

            .custom-btn-primary:hover {
                background: linear-gradient(135deg, #2563eb, #60a5fa);
                transform: translateY(-2px);
                color: #fff;
            }

            /* Line Items Modal Table Styling */
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f8f9fa;
            }

            .table-hover tbody tr:hover {
                background-color: #e9ecef;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #dee2e6;
            }

            .table-dark th {
                background-color: #343a40;
                color: white;
                font-weight: 600;
            }

            /* Increase width of the line items modal dialog */
            #lineItemsModal .modal-dialog {
                max-width: 90%;
                width: 1200px;
            }

            #lineItemsModal .modal-content {
                overflow-x: auto;
            }

            #lineItemsModal .table {
                width: 100%;
                table-layout: fixed;
            }

            #lineItemsModal .table th,
            #lineItemsModal .table td {
                word-wrap: break-word;
                white-space: normal;
            }

            #lineItemsModal .modal-body {
                padding: 1.5rem;
            }

            @media (max-width: 1200px) {
                #lineItemsModal .modal-dialog {
                    max-width: 80%;
                    width: 1000px;
                }
            }

            /* Orders Table Styling in New Dispatch Modal */
            #ordersTable th,
            #ordersTable td {
                text-align: center;
                vertical-align: middle;
            }

            #ordersTable input[type="checkbox"] {
                margin: 0 auto;
            }

            body.modal-open {
                overflow: auto !important;
                padding-right: 0 !important;
            }
        </style>
    @endpush


@endsection
