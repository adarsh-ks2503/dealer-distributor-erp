@extends('layouts.main')
@section('title', 'Order Report - Singhal Steel')
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
            'heading' => 'Orders Report',
            'sub_heading' => 'Orders Report',
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
                $('#OrderNumberDropdown').val(),
                getFilterTypeValue()
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
                                <label for="" class="mb-2"><strong>Order Number</strong></label>
                                <select class="custom-select form-control" id="OrderNumberDropdown">
                                    <option value="" disabled>Select</option>
                                    <option value="all" selected>All</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->order_number }}" @if ($order->id == $selectedId) selected @endif>{{ $order->order_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-12 mt-3">
                                <label for="" class="mb-2"><strong>Status</strong></label>
                                <select class="form-control" id="filterType" multiple style="height: 100px;">
                                    <option value="pending" {{ in_array('pending', $types ?? []) ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ in_array('approved', $types ?? []) ? 'selected' : '' }}>Approved</option>
                                    <option value="partial dispatch" {{ in_array('partial dispatch', $types ?? []) ? 'selected' : '' }}>Partial Dispatch</option>
                                    <option value="completed" {{ in_array('completed', $types ?? []) ? 'selected' : '' }}>Completed</option>
                                    <option value="rejected" {{ in_array('rejected', $types ?? []) ? 'selected' : '' }}>Rejected</option>
                                    <option value="closed with condition" {{ in_array('closed with condition', $types ?? []) ? 'selected' : '' }}>Closed With Condition</option>
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
                            <table id="orders_report_table" class="display stripe row-border order-column"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S. NO</th>
                                        <th>ORDER NO</th>
                                        <th>ORDER DATE</th>
                                        <th>DEALER/ <br>DISTRIBUTOR NANE</th>
                                        <th>ORDER TYPE</th>
                                        <th>ORDER <br> QUANTITY (MT)</th>
                                        <th>DISPATCHED <br> QUANTITY (MT)</th>
                                        <th>REMAINING <br> QUANTITY (MT)</th>
                                        {{-- <th>PRICE PER MT</th> --}}
                                        <th>TOTAL AMOUNT</th>
                                        <th>TOTAL TOKEN</th>
                                        {{-- <th>Payment Term</th> --}}
                                        <th>STATUS</th>
                                        <th>CREATED BY</th>
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
    </main>
    <script>
        // Helper function to get filter type value as string or 'all'
        function getFilterTypeValue() {
            const selectedValues = $('#filterType').val();
            if (!selectedValues || selectedValues.length === 0) {
                return 'all';
            }
            return selectedValues.join(',');
        }

        // Define the filterButton function first
        function filterButton(filterFromdate, filterTodate, DealersNameDropdown, DistributorsNameDropdown,
            OrderNumberDropdown, filterType) {
            $.ajax({
                type: 'POST',
                url: '{{ route('get_order.report') }}',
                data: {
                    from_date: filterFromdate,
                    to_date: filterTodate,
                    dealer_name: DealersNameDropdown,
                    distributer_name: DistributorsNameDropdown,
                    order_number: OrderNumberDropdown,
                    type: filterType,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    var table = $('#orders_report_table').DataTable();
                    table.clear().draw();
                    let rows = [];

                    res.forEach(function(item, index) {
                        console.log(item);
                        let dt = new Date(item.order_date);
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


                        // let agreedPrice = parseFloat(item.agreed_basic_price ?? 0);
                        // let insurance = parseFloat(item.insurance_charge ?? 0);
                        // let loading = parseFloat(item.loading_charge ?? 0);

                        // let totalAmount = agreedPrice + insurance + loading;
                        let orderLink =
                            `<a href="{{ route('order_management.show', '') }}/${item.id}">${item.order_number ?? 'N/A'}</a>`;
                        // console.log(orderLink);

                        rows.push([
                            index + 1, // 1. S. NO
                            orderLink, // 2. ORDER NO
                            fullFormatted, // 3. ORDER DATE
                            (item.dealer?.name) ? item.dealer.name : (item.distributor?.name ??
                                'N/A'), // 4. DEALER/DISTRIBUTOR NAME
                            item.type ? item.type.charAt(0).toUpperCase() + item.type.slice(1) :
                            'N/A', // 5. ORDER TYPE

                            // --- Yahaan se Blade Logic Shuru ---

                            // 6. ORDER QUANTITY (MT) (Aapke Blade jaisa Button)
                            (() => {
                                const totalQty = item.allocations?.reduce((total, alloc) =>
                                    total + (parseFloat(alloc.qty) || 0), 0) ?? 0;
                                // Button HTML (Aapke Blade file se)
                                return `<span>
                    ${totalQty} MT
                </span>`;
                            })(),

                            // 7. DISPATCHED QUANTITY (MT)
                            (() => {
                                const dispatchedQty = item.allocations?.reduce((total, alloc) =>
                                    total + (parseFloat(alloc.dispatched_qty) || 0), 0) ?? 0;
                                return `<span>
                    ${dispatchedQty} MT
                </span>`;
                            })(),

                            // 8. REMAINING QUANTITY (MT)
                            (() => {
                                const remainingQty = item.allocations?.reduce((total, alloc) =>
                                    total + (parseFloat(alloc.remaining_qty) || 0), 0) ?? 0;
                                return `<span>
                    ${remainingQty} MT
                </span>`;
                            })(),

                            // 9. TOTAL AMOUNT (Aapke Blade jaisa Calculation)
                            (() => {
                                const insurance = parseFloat(item.insurance_charge) || 0;
                                const loading = parseFloat(item.loading_charge) || 0;

                                const totalAmount = item.allocations?.reduce((total,
                                    alloc) => {
                                        const qty = parseFloat(alloc.qty) || 0;
                                        const price = parseFloat(alloc
                                            .agreed_basic_price) || 0;
                                        return total + (price + insurance + loading) *
                                            qty; // Total amount calculation
                                    }, 0) ?? 0;

                                // Currency formatting
                                return new Intl.NumberFormat('en-IN', {
                                    style: 'currency',
                                    currency: 'INR'
                                }).format(totalAmount);
                            })(),

                            // 10. TOTAL TOKEN (Aapke Blade jaisa Calculation)
                            (() => {
                                const totalToken = item.allocations?.reduce((total,
                                    alloc) => total + (parseFloat(alloc.token_amount) ||
                                        0), 0) ?? 0;

                                // Currency formatting
                                return (totalToken > 0) ? new Intl.NumberFormat('en-IN', {
                                    style: 'currency',
                                    currency: 'INR'
                                }).format(totalToken) : 'N/A';
                            })(),

                            // 11. STATUS (Aapke Blade jaisa Badge)
                            (() => {
                                const statusClasses = {
                                    'pending': 'bg-warning',
                                    'approved': 'bg-success',
                                    'partial dispatch': 'bg-info',
                                    'completed': 'bg-primary',
                                    'rejected': 'bg-danger',
                                    'closed with condition': 'bg-secondary',
                                };
                                const statusText = item.status ? item.status.charAt(0)
                                    .toUpperCase() + item.status.slice(1) : 'Pending';
                                const statusClass = statusClasses[item.status
                                ?.toLowerCase()] || 'bg-secondary';

                                // Badge HTML
                                return `<span class="badge ${statusClass}">${statusText}</span>`;
                            })(),

                            // 12. CREATED BY
                            item.created_by ?? 'N/A'
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
            // Parse URL params for dates and type
            const urlParams = new URLSearchParams(window.location.search);
            let initialFrom = urlParams.get('from_date') || '{{ $fromValue ?? "" }}';
            let initialTo = urlParams.get('to_date') || '{{ $toValue ?? "" }}';
            let initialType = 'all';
            const typesFromUrl = [];
            if (urlParams.has('type')) {
                const typeParam = decodeURIComponent(urlParams.get('type'));
                typesFromUrl.push(...typeParam.split(',').map(t => t.trim()));
                initialType = typesFromUrl.join(',');
            }

            // Set dates and types
            $('#filterFromdate').val(initialFrom);
            $('#filterTodate').val(initialTo);
            $('#filterType').val(typesFromUrl);

            // Initialize Select2 first
            $("#DealersNameDropdown").select2();
            $("#DistributorsNameDropdown").select2();
            $("#OrderNumberDropdown").select2();
            $("#filterType").select2({
                placeholder: "Select Statuses",
                allowClear: true
            });

            // Trigger change after setting val
            if (typesFromUrl.length > 0) {
                $('#filterType').trigger('change');
            }

            // Call filterButton when document is ready
            filterButton(
                initialFrom,
                initialTo,
                $('#DealersNameDropdown').val(),
                $('#DistributorsNameDropdown').val(),
                $('#OrderNumberDropdown').val(),
                initialType
            );

            // Filter button onClick handler
            $(document).on('click', '#filterButton', function() {
                filterButton(
                    $('#filterFromdate').val(),
                    $('#filterTodate').val(),
                    $('#DealersNameDropdown').val(),
                    $('#DistributorsNameDropdown').val(),
                    $('#OrderNumberDropdown').val(),
                    getFilterTypeValue()
                );
            });

            // Reset button functionality - reset to default (FY or current month based on access)
            $('#resetButton').click(function() {
                $('#filterFromdate').val('{{ $fromValue ?? "" }}');
                $('#filterTodate').val('{{ $toValue ?? "" }}');
                $('#filterType').val(null).trigger('change');
                $('#DealersNameDropdown').val('all').trigger('change');
                $('#DistributorsNameDropdown').val('all').trigger('change');
                $('#OrderNumberDropdown').val('all').trigger('change');
                filterButton(
                    '{{ $fromValue ?? "" }}',
                    '{{ $toValue ?? "" }}',
                    'all',
                    'all',
                    'all',
                    'all'
                );
            });
        });
    </script>
@endsection
