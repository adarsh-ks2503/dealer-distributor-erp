@extends('layouts.main')
@section('title', 'Dispatch Management')

@section('content')

<main id="main" class="main">
    @if ($message = Session::get('success'))
        <div class="tt active">
            <div class="tt-content">
                <i class="fas fa-solid fa-check check"></i>
                <div class="message">
                    <span class="text text-1">Success</span>
                    <span class="text text-2">{{ $message }}</span>
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="pg active"></div>
        </div>
    @endif

    @if ($errors->any())
        <div class="tt active">
            <div class="tt-content">
                <i class="fas fa-solid fa-xmark-circle error"></i>
                <div class="message">
                    <span class="text text-1">Error</span>
                    <span class="text text-2">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </span>
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="pg active"></div>
        </div>
    @endif

    <div class="dashboard-header pagetitle">
        <h1>Dispatch Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Dispatch</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-2">
                    <div class="card-body mt-4">
                        @can('Dispatch-Create')
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div></div>
                                <button class="btn custom-btn-primary" data-bs-toggle="modal" data-bs-target="#newDispatchModal">
                                    Add New Dispatch
                                </button>
                            </div>
                        @endcan

                        <div class="table-responsive">
                            <table class="table order-table text-center align-middle" id="dispatch_table">
                                <thead>
                                    <tr>
                                        <th class="text__left">#</th>
                                        <th class="text__left">Dispatch No.</th>
                                        <th class="text__left">Recipient Name</th>
                                        <th class="text__left">Type</th>
                                        <th class="text__left">Dispatch Date</th>
                                        <th class="text__left">Dispatch Time</th>
                                        <th class="text__left">Dispatched Qty</th>
                                        <th class="text__left">Total Items</th>
                                        <th class="text__left">Vehicle No</th>
                                        <th class="text__left">Driver Name</th>
                                        <th class="text__left">Warehouse</th>
                                        <th class="text__left">Status</th>
                                        <th class="text__left">Created By</th>
                                        <th class="text__left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dispatches as $i => $item)
                                        <tr>
                                            <td class="text__left">{{ $i+1 }}</td>
                                            <td class="text__left"><a href="{{ route('dispatch.report') }}">{{ $item->dispatch_number }}</a></td>
                                            <td class="text__left">{{ $item->recipient_name }}</td>
                                            <td class="text__left">{{ ucfirst($item->type) }}</td>
                                            <td class="text__left" data-order="{{ $item->dispatch_date }}">{{ \Carbon\Carbon::parse($item->dispatch_date)->format('d-M-Y') }}</td>
                                            <td class="text__left">
                                                @php
                                                    $time = 'N/A';
                                                    if ($item->dispatch_out_time) {
                                                        try {
                                                            $parsed = \Carbon\Carbon::createFromFormat('H:i', $item->dispatch_out_time)
                                                                ?: \Carbon\Carbon::createFromFormat('H:i:s', $item->dispatch_out_time);
                                                            $time = $parsed ? $parsed->format('h:i A') : 'N/A';
                                                        } catch (\Exception $e) {
                                                            $time = 'Invalid';
                                                        }
                                                    }
                                                @endphp
                                                {{ $time }}
                                            </td>
                                            @php
                                                $dispatchwt = $item->dispatchItems->sum('dispatch_qty');
                                            @endphp
                                            <td class="text__left">{{ number_format($dispatchwt , 2) }} MT</td>
                                            <td class="text__left">
                                                <button class="btn btn-outline-info btn-sm view-line-items-btn"
                                                    data-dispatch-num="{{ $item->dispatch_number }}"
                                                    data-dispatch-id="{{ $item->id }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#lineItemsModal">
                                                    {{ $item->dispatchItems->count() }} Items
                                                </button>
                                            </td>
                                            <td class="text__left">{{ $item->vehicle_no ?? 'N/A' }}</td>
                                            <td class="text__left">{{ $item->driver_name ?? 'N/A' }}</td>
                                            <td class="text__left">{{ $item->warehouse ? $item->warehouse->name : 'N/A' }}</td>
                                            <td class="text__left">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-warning',
                                                        'approved' => 'bg-success',
                                                        'partial dispatch' => 'bg-info',
                                                        'completed' => 'bg-primary',
                                                    ];
                                                    $statusClass = $statusClasses[strtolower($item->status)] ?? 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                            </td>
                                            <td class="text__left">{{ $item->created_by ?? 'N/A' }}</td>
                                            <td class="text__left">
                                                <div class="dropdown">
                                                    <button class="btn action-btn dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu animated-dropdown">
                                                        @can('Dispatch-View')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('dispatch.show', $item->id) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i>View
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('Dispatch-ExportPdf')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('dispatch.pdf.download', $item->id) }}">
                                                                    <i class="fa fa-file-pdf me-2 text-danger"></i>Export PDF
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('Dispatch-Edit')
                                                            @if ($item->status !== 'Approved')
                                                                <li>
                                                                    <a class="dropdown-item edit-btn" href="{{ route('dispatch.edit', $item->id) }}"
                                                                       data-dispatch-id="{{ $item->id }}">
                                                                        <i class="fa-solid fa-pen-to-square text-warning"></i>Edit
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                                        @can('Dispatch-Approve')
                                                            @if ($item->status !== 'Approved')
                                                                <li>
                                                                    <a class="dropdown-item approve-btn" href="#"
                                                                       data-dispatch-id="{{ $item->id }}">
                                                                        <i class="fa fa-check me-2 text-success"></i>Approve
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                                        @can('Dispatch-Approve')
                                                            @if (strtolower($item->status) === 'pending')
                                                                <li>
                                                                    <a class="dropdown-item text-danger delete-dispatch-btn" href="#"
                                                                    data-dispatch-id="{{ $item->id }}"
                                                                    data-dispatch-number="{{ $item->dispatch_number }}">
                                                                        <i class="fa fa-trash-alt me-2"></i>Delete
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to <strong>permanently delete</strong> dispatch <span id="deleteDispatchNumber" class="text-danger"></span>?
                    <br><br>
                    <small class="text-muted">This action cannot be undone.</small>
                </div>
                <div class="modal-footer">
                    <form id="deleteDispatchForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Dispatch Modal -->
    <div class="modal fade" id="newDispatchModal" tabindex="-1" aria-labelledby="newDispatchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="dispatchForm" method="GET" action="{{ route('dispatch.create') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Dispatch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Order Type -->
                            <div class="col-md-6">
                                <label for="order_type" class="form-label">Order Type <span class="text-danger">*</span></label>
                                <select name="order_type" id="order_type" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="distributor">Distributor</option>
                                    <option value="dealer">Dealer</option>
                                </select>
                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dealer/Distributor -->
                            <div class="col-md-6">
                                <label for="party_id" class="form-label">Select Dealer/Distributor <span class="text-danger">*</span></label>
                                <select name="party_id" id="party_id" class="form-select select2-party" required>
                                    <option value="">-- Select --</option>
                                </select>
                                @error('distributor_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('dealer_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Orders Table -->
                            <div class="col-md-12">
                                <label class="form-label">Select Orders <span class="text-danger">*</span></label>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered text-center" id="ordersTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Select</th>
                                                <th>Creation Date</th>
                                                <th>Order Number</th>
                                                <th>Ordered Qty (MT)</th>
                                                <th>Remaining Qty (MT)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ordersTableBody">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>
                                @error('order_ids')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <div id="orderError" class="text-danger" style="display: none;">Please select at least one order.</div>
                            </div>

                            <!-- Loading Point -->
                            <div class="col-md-12">
                                <label for="warehouse_id" class="form-label">Select Warehouse <span class="text-danger">*</span></label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('loading_point_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-4">
                        <button type="submit" class="btn btn-primary">Proceed</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Line Items Modal -->
    <div class="modal fade" id="lineItemsModal" tabindex="-1" aria-labelledby="lineItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dispatch Line Items - <span id="dispatchNumberPlaceholder"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="lineItemsLoader" class="text-center py-4" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Loading line items...</p>
                    </div>

                    <div id="lineItemsContent" style="display: none;">
                        <table class="table table-striped table-hover table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Order Number</th>
                                    <th>Item Name</th>
                                    <th>Size (mm)</th>
                                    <th>Dispatch Qty (MT)</th>
                                    <th>Basic Price (₹/MT)</th>
                                    <th>Gauge Diff (₹)</th>
                                    <th>Final Price (₹/MT)</th>
                                    <th>GST (%)</th>
                                    <th>Total Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="lineItemsTableBody">
                                <!-- Dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div class="modal fade" id="approveConfirmModal" tabindex="-1" aria-labelledby="approveConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve this dispatch?
                </div>
                <div class="modal-footer">
                    <form id="approveDispatchForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">Yes, Approve</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message Modal -->
    @if ($message = Session::get('error'))
        <div class="modal fade" id="errorMessageModal" tabindex="-1" aria-labelledby="errorMessageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="errorMessageModalLabel">Warning</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-solid fa-xmark-circle error me-3" style="font-size: 24px; color: #dc3545;"></i>
                            <span>{{ $message }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</main>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            max-width: 90%; /* Increase to 90% of the viewport width */
            width: 1200px; /* Set a specific minimum width */
        }

        /* Ensure the table stays within the modal content */
        #lineItemsModal .modal-content {
            overflow-x: auto; /* Add horizontal scroll if content overflows */
        }

        #lineItemsModal .table {
            width: 100%; /* Ensure table takes full width of the modal content */
            table-layout: fixed; /* Prevent column width issues */
        }

        #lineItemsModal .table th,
        #lineItemsModal .table td {
            word-wrap: break-word; /* Allow long text to break into multiple lines */
            white-space: normal; /* Prevent text from overflowing */
        }

        /* Adjust padding and margins for better fit */
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

        /* Select2 Styling */
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Client-side Indian number formatting function
        function formatIndianCurrency(number) {
            if (isNaN(number) || number === null) return '₹0.00';
            number = parseFloat(number).toFixed(2);
            let [integer, decimal] = number.split('.');
            let lastThree = integer.slice(-3);
            let otherNumbers = integer.slice(0, -3);
            if (otherNumbers !== '') {
                lastThree = ',' + lastThree;
            }
            let result = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + lastThree;
            return '₹' + result + '.' + decimal;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for party_id dropdown
            $('#party_id').select2({
                placeholder: "-- Select Dealer/Distributor --",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#newDispatchModal')
            });

            const alert = document.querySelector('.tt.active');
            if (alert) {
                setTimeout(() => {
                    alert.classList.remove('active');
                }, 5000);
            }

            // Automatically show error modal if error exists
            @if ($message = Session::get('error'))
                const errorModal = new bootstrap.Modal(document.getElementById('errorMessageModal'));
                errorModal.show();
            @endif

            // Form validation for at least one order selection
            const dispatchForm = document.getElementById('dispatchForm');
            dispatchForm.addEventListener('submit', function(event) {
                const checkboxes = document.querySelectorAll('#ordersTableBody input[name="order_ids[]"]:checked');
                const orderError = document.getElementById('orderError');
                if (checkboxes.length === 0) {
                    event.preventDefault();
                    orderError.style.display = 'block';
                } else {
                    orderError.style.display = 'none';
                }
            });
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        const deleteForm = document.getElementById('deleteDispatchForm');
        const deleteNumberSpan = document.getElementById('deleteDispatchNumber');

        document.querySelectorAll('.delete-dispatch-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const dispatchId = this.getAttribute('data-dispatch-id');
                const dispatchNumber = this.getAttribute('data-dispatch-number');

                deleteNumberSpan.textContent = dispatchNumber;
                deleteForm.setAttribute('action', `/dispatch/${dispatchId}`);
                deleteModal.show();
            });
        });
    });
</script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const orderTypeSelect = document.getElementById('order_type');
            const partySelect = document.getElementById('party_id');
            const ordersTableBody = document.getElementById('ordersTableBody');

            orderTypeSelect.addEventListener('change', function() {
                const type = this.value;
                partySelect.innerHTML = '<option value="">Loading...</option>';
                $('#party_id').val(null).trigger('change'); // Reset Select2
                ordersTableBody.innerHTML = ''; // Clear orders table
                if (type) {
                    fetch(`/api/party-list?type=${type}`)
                        .then(res => {
                            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                            return res.json();
                        })
                        .then(data => {
                            console.log('Party list response:', data); // Debug log
                            partySelect.innerHTML = '<option value="">-- Select --</option>';
                            if (data.length === 0) {
                                partySelect.innerHTML = '<option value="">No parties found</option>';
                            } else {
                                data.forEach(item => {
                                    const option = new Option(item.name, item.id);
                                    partySelect.appendChild(option);
                                });
                            }
                            $('#party_id').trigger('change'); // Refresh Select2
                        })
                        .catch(err => {
                            console.error('Error fetching parties:', err);
                            partySelect.innerHTML = '<option value="">Error loading data</option>';
                            $('#party_id').trigger('change'); // Refresh Select2
                        });
                } else {
                    partySelect.innerHTML = '<option value="">-- Select --</option>';
                    $('#party_id').trigger('change'); // Refresh Select2
                }
            });

            // Use Select2's select2:select event instead of native change
            $('#party_id').on('select2:select', function(e) {
                const type = orderTypeSelect.value;
                const partyId = e.target.value;
                console.log('Party selected:', { type, partyId }); // Debug log
                ordersTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Loading...</td></tr>';
                if (type && partyId) {
                    fetch(`/api/order-list?type=${type}&party_id=${partyId}`)
                        .then(res => {
                            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                            return res.json();
                        })
                        .then(data => {
                            console.log('Order list response:', data); // Debug log
                            ordersTableBody.innerHTML = '';
                            if (data.length === 0) {
                                ordersTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No orders found.</td></tr>';
                            } else {
                                data.forEach(order => {
                                    const creationDate = order.created_at ? new Date(order.created_at).toLocaleDateString('en-GB') : 'N/A';
                                    const orderedQty = order.allocations && order.allocations.length > 0
                                        ? (order.allocations.reduce((sum, alloc) => sum + (parseFloat(alloc.qty) || 0), 0))
                                        : '0.00';
                                    const remainingQty = order.allocations && order.allocations.length > 0
                                        ? (order.allocations.reduce((sum, alloc) => sum + (parseFloat(alloc.remaining_qty) || 0), 0))
                                        : '0.00';
                                    const totalRemaining = order.total_remaining_qty;
                                    const row = `
                                        <tr>
                                            <td><input type="checkbox" name="order_ids[]" value="${order.id}"></td>
                                            <td>${creationDate}</td>
                                            <td>${order.order_number}</td>
                                            <td>${orderedQty}</td>
                                            <td>${totalRemaining}</td>
                                        </tr>
                                    `;
                                    ordersTableBody.innerHTML += row;
                                });
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching orders:', err);
                            ordersTableBody.innerHTML = '<tr><td colspan="5" class="text-danger text-center">Error loading data.</td></tr>';
                        });
                } else {
                    ordersTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Please select order type and party.</td></tr>';
                }
            });

            // Clear orders table when party is cleared
            $('#party_id').on('select2:unselect', function() {
                ordersTableBody.innerHTML = '';
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = new bootstrap.Modal(document.getElementById('lineItemsModal'));
            const buttons = document.querySelectorAll('.view-line-items-btn');
            const loader = document.getElementById('lineItemsLoader');
            const content = document.getElementById('lineItemsContent');
            const tbody = document.getElementById('lineItemsTableBody');

            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const dispatchId = this.getAttribute('data-dispatch-id');
                    const dispatchNum = this.getAttribute('data-dispatch-num');

                    document.getElementById('dispatchNumberPlaceholder').textContent = dispatchNum;
                    loader.style.display = 'block';
                    content.style.display = 'none';
                    tbody.innerHTML = '';

                    fetch(`/api/dispatch/${dispatchId}/items`, {
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache'
                        }
                    })
                        .then(res => {
                            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                            return res.json();
                        })
                        .then(data => {
                            console.log('API Response:', data);
                            if (Array.isArray(data) && data.length) {
                                data.forEach((item, index) => {
                                    console.log('Item Data:', item);
                                    const orderNumber = item.order?.order_number || 'N/A';
                                    const itemName = item.item?.item_name || 'N/A';
                                    const size = item.size ? item.size.size : 'N/A';
                                    const dispatchQty = item.dispatch_qty !== null && !isNaN(item.dispatch_qty) ? (Number(item.dispatch_qty)) : 'N/A';
                                    const basicPrice = item.basic_price !== null && !isNaN(item.basic_price) ? formatIndianCurrency(Number(item.basic_price)) : 'N/A';
                                    const gaugeDiff = item.gauge_diff !== null && !isNaN(item.gauge_diff) ? formatIndianCurrency(Number(item.gauge_diff)) : 'N/A';
                                    const finalPrice = item.final_price !== null && !isNaN(item.final_price) ? formatIndianCurrency(Number(item.final_price)) : 'N/A';
                                    const gst = item.gst !== null && !isNaN(item.gst) ? Number(item.gst).toFixed(2) : 'N/A';
                                    const totalAmount = item.total_amount !== null && !isNaN(item.total_amount) ? formatIndianCurrency(Number(item.total_amount)) : 'N/A';

                                    const row = `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${orderNumber}</td>
                                            <td>${itemName}</td>
                                            <td>${size}</td>
                                            <td>${dispatchQty}</td>
                                            <td>${basicPrice}</td>
                                            <td>${gaugeDiff}</td>
                                            <td>${finalPrice}</td>
                                            <td>${gst}</td>
                                            <td>${totalAmount}</td>
                                        </tr>
                                    `;
                                    tbody.innerHTML += row;
                                });
                            } else {
                                tbody.innerHTML = '<tr><td colspan="11" class="text-center">No line items found.</td></tr>';
                            }
                            loader.style.display = 'none';
                            content.style.display = 'block';
                        })
                        .catch(err => {
                            console.error('Error fetching line items:', err);
                            loader.style.display = 'none';
                            tbody.innerHTML = '<tr><td colspan="11" class="text-danger text-center">Failed to load data. Check console for details.</td></tr>';
                            content.style.display = 'block';
                        });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const approveButtons = document.querySelectorAll('.approve-btn');
            const approveModal = new bootstrap.Modal(document.getElementById('approveConfirmModal'));
            const approveForm = document.getElementById('approveDispatchForm');

            approveButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dispatchId = this.getAttribute('data-dispatch-id');
                    approveForm.setAttribute('action', `/dispatch/${dispatchId}/approve`);
                    approveModal.show();
                });
            });
        });
    </script>
@endpush

@endsection
