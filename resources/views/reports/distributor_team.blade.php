@extends('layouts.main')
@section('title', 'Distributor Team Report - Singhal Steel')
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
            'heading' => 'Distributor Team Report',
            'sub_heading' => 'Distributor Team Report',
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
                $('#DistributorsNameDropdown').val(),
                $('#DistributorCodeDropdown').val(),
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
                            </div> --}}
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Distributor Name</strong></label>
                                <select class="custom-select form-control" id="DistributorsNameDropdown">
                                    <option value="" disabled>Select Distributor</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($distributors as $distributor)
                                        <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Distributor Code</strong></label>
                                <select class="custom-select form-control" id="DistributorCodeDropdown">
                                    <option value="" disabled>Select</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($distributors as $distributor)
                                        <option value="{{ $distributor->code }}">{{ $distributor->code }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
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
                            <table id="distributor_team_report_table" class="display stripe row-border order-column"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S. NO</th>
                                        <th>DISTRIBUTOR NAME</th>
                                        <th>DISTRIBUTOR CODE</th>
                                        <th>STATE</th>
                                        <th>MOBILE NO.</th>
                                        <th>ASSIGNED DEALERS</th>
                                        <th>TOTAL ORDER <br> LIMIT (MT)</th>
                                        <th>ORDERED QTY (MT)</th>
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


        <div class="modal fade" id="teamDealersModal" tabindex="-1" aria-labelledby="teamDealersModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dealers in Team - <span id="teamDistributorNamePlaceholder"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="teamDealersLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading dealers...</p>
                        </div>
                        <div id="teamDealersContent" style="display: none;">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Dealer Name</th>
                                        <th>Code</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="teamDealersTableBody">
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
        function filterButton(filterFromdate, filterTodate, DistributorsNameDropdown, DistributorCodeDropdown,
            StateDropdown, filterType) {
            $.ajax({
                type: 'POST',
                url: '{{ route('get_distributor_team.report') }}',
                data: {
                    from_date: filterFromdate,
                    to_date: filterTodate,
                    distributer_name: DistributorsNameDropdown,
                    distributor_code: DistributorCodeDropdown,
                    state: StateDropdown,
                    type: filterType,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    var table = $('#distributor_team_report_table').DataTable();
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

                        let fullFormatted = `${formattedDate} ${time}`;

                        let dealersCountCell = `<span" class="view-team-dealers-btn" 
                               data-team-id="${item.id}" 
                               data-distributor-name="${item.distributor.name}"
                               style="color: #0d6efd; cursor: pointer;">
                               ${item.no_of_dealers}
                           </span>`;

                           const currencyFormat = { style: 'currency', currency: 'INR', minimumFractionDigits: 2, maximumFractionDigits: 2 };

                        rows.push([
                            index + 1,
                            item.distributor.name ?? 'N/A',
                            item.distributor.code ?? 'N/A',
                            item.distributor?.state?.state || 'N/A',
                            item.distributor.mobile_no ?? 'N/A',
                            // item.dealers[0]?.name ?? '0',
                            // item.no_of_dealers ?? '0',
                            dealersCountCell,
                            // item.total_order_limit ?? '0',
                            item.total_order_limit + ' MT',
                            item.ordered_quantity ?? '0',
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
                $('#DistributorCodeDropdown').val(),
                $('#StateDropdown').val(),
                $('#filterType').val(),
            );

            // Filter button onClick handler
            $('#filterButton').click(function() {
                filterButton(
                    $('#filterFromdate').val(),
                    $('#filterTodate').val(),
                    $('#DistributorsNameDropdown').val(),
                    $('#DistributorCodeDropdown').val(),
                    $('#StateDropdown').val(),
                    $('#filterType').val(),
                );
            });

            // Reset button functionality
            $('#resetButton').click(function() {
                location.reload();
            });

            // Select2 initialization
            $("#DistributorsNameDropdown").select2();
            $("#DistributorCodeDropdown").select2();
            $("#StateDropdown").select2();
            $("#filterType").select2();
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Modal ke elements ko select karein
            const teamModal = new bootstrap.Modal(document.getElementById('teamDealersModal'));
            const loader = document.getElementById('teamDealersLoader');
            const content = document.getElementById('teamDealersContent');
            const tbody = document.getElementById('teamDealersTableBody');
            const distributorNamePlaceholder = document.getElementById('teamDistributorNamePlaceholder');

            // Click event ko handle karein
            $('#distributor_team_report_table tbody').on('click', '.view-team-dealers-btn', function(event) {
                event.preventDefault();
                const teamId = this.getAttribute('data-team-id');
                const distributorName = this.getAttribute('data-distributor-name');

                // Modal taiyaar karein
                distributorNamePlaceholder.textContent = distributorName;
                loader.style.display = 'block';
                content.style.display = 'none';
                tbody.innerHTML = '';

                // API se team ke dealers ka data fetch karein
                fetch(`/reports/distributor-teams/${teamId}/dealers`)
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach((dealer, index) => {
                                const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td><a href="{{ route('dealers.show', '') }}/${dealer.id}">
                ${dealer.name || 'N/A'}
            </a></td>
                                <td>${dealer.code || 'N/A'}</td>
                                <td>${dealer.city ? dealer.city.name : 'N/A'}</td>
                                <td>${dealer.state ? dealer.state.state : 'N/A'}</td>
                                <td>${dealer.status || 'N/A'}</td>
                            </tr>
                        `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="6" class="text-center">No dealers found in this team.</td></tr>';
                        }
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching team dealers:', error);
                        tbody.innerHTML =
                            '<tr><td colspan="6" class="text-center text-danger">Failed to load dealers.</td></tr>';
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    });

                teamModal.show();
            });
        });
    </script>
@endsection
