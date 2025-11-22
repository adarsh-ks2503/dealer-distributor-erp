@extends('layouts.main')
@section('title', 'Distributors Report - Singhal Steel')
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
            #distributorHistoryTableBody {
                overflow-x: auto;
            }
        }
    </style>

    <main id="main" class="main">

        @include('partials.heading_cards.cards', [
            'heading' => 'Distributors Report',
            'sub_heading' => 'Distributors Report',
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


                            {{-- <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Dealers</strong></label>
                                <select class="custom-select form-control" id="DealersNameDropdown">
                                    <option value="" disabled>Select Dealer</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($dealers as $dealer)
                                        <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Distributor</strong></label>
                                <select class="custom-select form-control" id="DistributorsNameDropdown">
                                    <option value="" disabled>Select Assigned Distributor</option>
                                    <option value="all" selected>All</option>

                                    @foreach ($distributors as $distributor)
                                        <option value="{{ $distributor->name }}">{{ $distributor->name }}</option>
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
                            <table id="distributors_report_table" class="display stripe row-border order-column"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        {{-- <th>Sl. No</th> --}}
                                        <th>S. No</th>
                                        <th>DISTRIBUTOR ID</th>
                                        <th>NAME</th>
                                        <th>CODE</th>
                                        <th>ORDER LIMIT</th>
                                        <th>ALLOWED <br> ORDER LIMIT</th>
                                        <th>INDIVIDUAL <br> ALLOWED <br> ORDER LIMIT
                                        </th>
                                        <th>STATE</th>
                                        <th>CITY</th>
                                        <th>MOBILE NO.</th>
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




        {{-- <div class="modal fade" id="distributorOrderLimitHistoryModal" tabindex="-1"
            aria-labelledby="distributorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Limit History - <span id="distributorNamePlaceholder"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="distributorHistoryLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading history...</p>
                        </div>
                        <div id="distributorHistoryContent" style="display: none;">
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
                                <tbody id="distributorHistoryTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="modal fade" id="distributorOrderLimitHistoryModal" tabindex="-1"
    aria-labelledby="distributorModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Limit History - <span id="distributorNamePlaceholder"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="distributorHistoryLoader" class="text-center py-4" style="display:none;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3">Loading history...</p>
                </div>
                
                <div id="distributorHistoryContent" style="display: none;">
                    
                    <div class="table-responsive">
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
                            <tbody id="distributorHistoryTableBody">
                            </tbody>
                        </table>
                    </div>
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
                        <h5 class="modal-title">Contact Persons - <span id="contactDistributorNamePlaceholder"></span></h5>
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
        function filterButton(filterFromdate, filterTodate, DistributorsNameDropdown,
            StateDropdown, CityDropdown, filterType) {
            $.ajax({
                type: 'POST',
                url: '{{ route('get_distributors.report') }}',
                data: {
                    from_date: filterFromdate,
                    to_date: filterTodate,
                    distributer_name: DistributorsNameDropdown,
                    state: StateDropdown,
                    city: CityDropdown,
                    type: filterType,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    var table = $('#distributors_report_table').DataTable();
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
                        const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };


                        let agreedPrice = parseFloat(item.agreed_basic_price ?? 0);
                        let insurance = parseFloat(item.insurance_charge ?? 0);
                        let loading = parseFloat(item.loading_charge ?? 0);

                        let totalAmount = agreedPrice + insurance + loading;

                        let distributorLink =
                            `<a href="{{ route('distributors.show', '') }}/${item.id}">${item.code ?? 'N/A'}</a>`;
                        // console.log(distributorLink);
                        // console.log(item.id);

                        // let orderLimitCell = `<span class="view-distributor-limit-history-btn" 
                        //     data-distributor-id="${item.id}" 
                        //     data-distributor-name="${item.name}"
                        //     style="color: #0d6efd; cursor: pointer;">
                        //     ${item.order_limit ?? '0'}
                        //   </span>`;

                        // Pehle value ko format karein
const formattedLimit = item.order_limit + ' MT';

// Ab formatted value ko HTML string mein daalein
let orderLimitCell = `<span class="view-distributor-limit-history-btn" 
                        data-distributor-id="${item.id}" 
                        data-distributor-name="${item.name}"
                        style="color: #0d6efd; cursor: pointer;">
                        ${formattedLimit}
                    </span>`;

                        let contactPersonCell = `<span class="view-distributor-contacts-btn"
                               data-distributor-id="${item.id}"
                               data-distributor-name="${item.name}"
                               style="color: #0d6efd; cursor: pointer;">
                               ${item.contact_persons.length}
                           </span>`;

                        rows.push([
                            index + 1,
                            // item.id ?? 'N/A',
                            distributorLink,
                            item.name ?? 'N/A',
                            item.code ?? 'N/A',
                            // item.order_limit ?? 'N/A',
                            orderLimitCell,
                            // item.allowed_order_limit ??
                            // 'N/A',
                            // item.individual_allowed_order_limit ??
                            // 'N/A',
                            // Allowed Order Limit
item.allowed_order_limit + ' MT',

// Individual Allowed Order Limit
item.individual_allowed_order_limit + ' MT',
                            item.state.state ?? 'N/A',
                            item.city.name ?? 'N/A',
                            item.mobile_no ?? 'N/A',
                            contactPersonCell,
                            item.status ?? 'N/A',
                            fullFormatted,
                        ]);


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
            // Modal elements
            const historyModal = new bootstrap.Modal(document.getElementById('distributorOrderLimitHistoryModal'));
            const loader = document.getElementById('distributorHistoryLoader');
            const content = document.getElementById('distributorHistoryContent');
            const tbody = document.getElementById('distributorHistoryTableBody');
            const distributorNamePlaceholder = document.getElementById('distributorNamePlaceholder');

            // Event listener for the clickable order limit number
            $('#distributors_report_table tbody').on('click', '.view-distributor-limit-history-btn', function(
                event) {
                event.preventDefault();
                const distributorId = this.getAttribute('data-distributor-id');
                const distributorName = this.getAttribute('data-distributor-name');

                // Prepare modal
                distributorNamePlaceholder.textContent = distributorName;
                loader.style.display = 'block';
                content.style.display = 'none';
                tbody.innerHTML = '';

                // Fetch history data from the server
                fetch(`/reports/distributors/${distributorId}/order-limit-history`)
                    .then(response => response.json())
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
            // Modal ke elements ko select karein
            const contactModal = new bootstrap.Modal(document.getElementById('contactPersonsModal'));
            const loader = document.getElementById('contactsLoader');
            const content = document.getElementById('contactsContent');
            const tbody = document.getElementById('contactsTableBody');
            const distributorNamePlaceholder = document.getElementById('contactDistributorNamePlaceholder');

            // Click event ko handle karein
            $('#distributors_report_table tbody').on('click', '.view-distributor-contacts-btn', function(event) {
                event.preventDefault();
                const distributorId = this.getAttribute('data-distributor-id');
                const distributorName = this.getAttribute('data-distributor-name');

                // Modal taiyaar karein
                distributorNamePlaceholder.textContent = distributorName;
                loader.style.display = 'block';
                content.style.display = 'none';
                tbody.innerHTML = '';

                // API se contact persons ka data fetch karein
                fetch(`/reports/distributors/${distributorId}/contact-persons`)
                    .then(response => response.json())
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
                                '<tr><td colspan="4" class="text-center">No contact persons found.</td></tr>';
                        }
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching contacts:', error);
                        tbody.innerHTML =
                            '<tr><td colspan="4" class="text-center text-danger">Failed to load contacts.</td></tr>';
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    });

                contactModal.show();
            });
        });
    </script>
@endsection
