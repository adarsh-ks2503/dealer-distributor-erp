@extends('layouts.main')
@section('title', 'Order Management - Singhal')
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
        <h1>Order Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Order Management</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-2">
                    <div class="card-body mt-4">
                        @can('Order-Create')
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center"></div>
                                <button type="button" class="btn custom-btn-primary ms-2" data-bs-toggle="modal"
                                    data-bs-target="#orderModal">
                                    Add New Order
                                </button>
                            </div>
                        @endcan

                        <div class="table-responsive">
                            <table class="table order-table text-center align-middle" id="orders_table">
                                <thead>
                                    <tr>
                                        <th class="text__left">#</th>
                                        <th class="text__left">ORDER No</th>
                                        <th class="text__left">ORDER Date</th>
                                        <th class="text__left">Dealer/Distributor Name</th>
                                        <th class="text__left">Order Type</th>
                                        <th class="text__left">Total Qty (MT)</th>
                                        {{-- <th class="text__left">Price per MT</th> --}}
                                        <th class="text__left">Total Amount</th>
                                        <th class="text__left">Total Token</th>
                                        <th class="text__left">Status</th>
                                        <th class="text__left">Created By</th>
                                        <th class="text__left">Status Changed</th>
                                        <th class="text__left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $i => $order)
                                        @php
                                            $totalQty = $order->allocations?->sum('qty');
                                            $totalToken = $order->allocations?->sum('token_amount');
                                            $totalAmount = $order->allocations?->sum(
                                                fn($a) => ($a->agreed_basic_price + $order->insurance_charge + $order->loading_charge) * $a->qty,
                                            );
                                            $grandTotal = $totalAmount;
                                            $avgPrice = $totalQty > 0 ? round($totalAmount / $totalQty, 2) : 0;
                                        @endphp
                                        <tr>
                                            <td class="text__left">{{ $i + 1 }}</td>
                                            <td class="text__left">{{ $order->order_number }}</td>

                                            <td class="text__left" data-order="{{ $order->order_date }}">{{ \Carbon\Carbon::parse($order->order_date)->format('d-M-Y') }}</td>
                                            <td class="text__left">
                                                @if ($order->type === 'dealer')
                                                    {{ $order->dealer->name ?? 'N/A' }}
                                                @else
                                                    {{ $order->distributor->name ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td class="text__left">{{ ucfirst($order->type) }}</td>
                                            <td class="text__left">
                                                <button type="button" class="btn btn-outline-info btn-sm view-allocations-btn"
                                                        data-bs-toggle="modal" data-bs-target="#allocationsModal"
                                                        data-order-id="{{ $order->id }}"
                                                        data-order-number="{{ $order->order_number }}">
                                                    {{ $totalQty }} MT
                                                </button>
                                            </td>
                                            {{-- <td class="text__left">{{ \App\Helpers\NumberHelper::formatIndianCurrency($avgPrice, 2) }}</td> --}}
                                            <td class="text__left">{{ \App\Helpers\NumberHelper::formatIndianCurrency($grandTotal, 2) }}</td>
                                            <td class="text__left">{{ $totalToken > 0 ? \App\Helpers\NumberHelper::formatIndianCurrency($totalToken, 2) : 'N/A' }}</td>
                                            <td class="text__left">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-warning',
                                                        'approved' => 'bg-success',
                                                        'partial dispatch' => 'bg-info',
                                                        'completed' => 'bg-primary',
                                                        'rejected' => 'bg-danger',
                                                        'closed with condition' => 'bg-secondary',
                                                    ];
                                                    $statusClass = $statusClasses[strtolower($order->status)] ?? 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                            </td>
                                            <td class="text__left">{{ $order->created_by }}</td>
                                            <td class="text__left">
                                                @if($order->status_changed_at)
                                                    {{ \Carbon\Carbon::parse($order->status_changed_at)->format('d-m-Y h:i A') }}<br>
                                                    <small>by {{ $order->status_changed_by }}</small>
                                                @else
                                                    "N/A"
                                                @endif
                                            </td>
                                            <td class="text__left">
                                                <div class="dropdown">
                                                    <button class="btn action-btn dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu animated-dropdown">
                                                        @can('Order-View')
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('order_management.show', $order->id) }}"><i
                                                                        class="fa fa-eye me-2 text-primary"></i>View</a>
                                                            </li>
                                                        @endcan
                                                        @if ($order->status == 'pending')
                                                            @can('Order-Edit')
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('order_management.edit', $order->id) }}"><i
                                                                            class="fa fa-edit me-2 text-warning"></i>Edit</a>
                                                                </li>
                                                            @endcan
                                                            @can('Order-Delete')
                                                                <li><a class="dropdown-item text-danger delete-btn"
                                                                        href="#"
                                                                        data-order-id="{{ $order->id }}">
                                                                        <i class="fa-solid fa-xmark text-danger"></i>Reject</a>
                                                                </li>
                                                            @endcan
                                                            @can('Order-Delete')
                                                                <li><a class="dropdown-item text-danger delete-order-btn"
                                                                        href="#"
                                                                        data-order-id="{{ $order->id }}">
                                                                        <i class="fa-solid fa-trash text-deanger"></i>Delete</a>
                                                                </li>
                                                            @endcan
                                                            @can('Order-Approve')
                                                                <li>
                                                                    <a class="dropdown-item approve-btn" href="#"
                                                                        data-order-id="{{ $order->id }}">
                                                                        <i class="fa fa-check me-2 text-success"></i>Approve
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        @endif
                                                        @can('Order-DownloadPdf')
                                                            <li><a class="dropdown-item" href="{{ route('order.pdf.download', $order->id) }}"><i
                                                                        class="fa fa-file-pdf me-2 text-danger"></i>Download
                                                                    PDF</a></li>
                                                        @endcan
                                                        @can('Order-ChangeStatus')
                                                            @if ($order->status === 'approved' || $order->status === 'partial dispatch' )
                                                                <li><a class="dropdown-item change-status-btn" href="#"
                                                                        data-order-id="{{ $order->id }}">
                                                                        <i class="fa fa-exchange-alt me-2 text-info"></i>Change Status
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
        </section>

        {{-- Approve Confirmation Modal --}}
        <div class="modal fade" id="approveModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Confirm Approval</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to approve this order?
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="approveForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success">Yes, Approve</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Confirm Rejection</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to reject this order?
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="rejectForm" method="GET" action="">
                            @csrf
                            <button type="submit" class="btn btn-danger">Yes, Reject</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteOrderModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this order?
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" action="">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Status Modal --}}
        <div class="modal fade" id="changeStatusModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Change Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="changeStatusForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Select Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="">Select Status</option>
                                    <option value="closed with condition">Close with Condition</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status Change Remarks</label>
                                <textarea name="status_change_remarks" id="status_change_remarks"
                                        class="form-control" rows="3"
                                        placeholder="Optional: Reason for status change..."></textarea>
                            </div>

                            <!-- Readonly Tracking Fields -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Changed By</label>
                                    <input type="text" id="changed_by_display" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Changed On</label>
                                    <input type="text" id="changed_at_display" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Allocations Modal --}}
        <div class="modal fade" id="allocationsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title text-dark" id="allocationsModalTitle">Order Allocations - <span id="orderNumberPlaceholder"></span></h5>
                        <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="allocationsLoader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Loading allocations...</p>
                        </div>
                        <div id="allocationsContent" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered text-center" id="allocationsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Allocated To</th>
                                            <th>Qty</th>
                                            <th>Basic Price (₹/MT)</th>
                                            <th>Agreed Price (₹/MT)</th>
                                            <th>Token Amount (₹)</th>
                                            <th>Payment Term</th>
                                            <th>Dispatched Qty</th>
                                            <th>Remaining Qty</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allocationsTableBody">
                                        <!-- Dynamically populated -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Modal -->
        <div class="modal fade" id="alertModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="alertModalLabel">Error</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="alertModalBody">
                        <!-- Message will be injected via JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    {{-- Order Modal --}}
    <div class="modal fade" id="orderModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Select Dealer/Distributor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="orderSelectForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Order Type <span class="text-danger">*</span></label>
                            <select name="order_type" id="order_type" class="form-select" required>
                                <option value="">Select Order Type</option>
                                <option value="dealer">Dealer</option>
                                <option value="distributor">Distributor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Dealer/Distributor <span
                                    class="text-danger">*</span></label>
                            <select name="party_id" id="party_id" class="form-select" required>
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn custom-btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.change-status-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const orderId = this.getAttribute('data-order-id');
                    const form = document.getElementById('changeStatusForm');
                    form.setAttribute('action', `/order-management/change-status/${orderId}`);

                    // Auto-fill changed by & date
                    const now = new Date();
                    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    const day = now.getDate();
                    const monthName = months[now.getMonth()];
                    const year = now.getFullYear();
                    const dateStr = `${day} ${monthName} ${year}`;
                    const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    document.getElementById('changed_by_display').value = "{{ auth()->user()->name }} {{ auth()->user()->last_name }}";
                    document.getElementById('changed_at_display').value = `${dateStr} ${timeStr}`;

                    const modal = new bootstrap.Modal('#changeStatusModal');
                    modal.show();
                });
            });
        });
    </script>
        <script>
            // Indian Currency Formatting
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

            // === MODAL: Close ONLY on X, Focus on outside click ===
            document.addEventListener('DOMContentLoaded', function () {
                // Clean backdrop on hide
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.addEventListener('hidden.bs.modal', function () {
                        document.body.classList.remove('modal-open');
                        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    });
                });

                // Click anywhere → Focus modal (don't close)
                document.addEventListener('click', function (e) {
                    const openModal = document.querySelector('.modal.show');
                    if (openModal && !openModal.contains(e.target)) {
                        // Clicked outside → Focus first input or modal
                        const firstInput = openModal.querySelector('input, select, textarea, button');
                        if (firstInput) firstInput.focus();
                    }
                });
            });

            // Approve Button
            document.querySelectorAll('.approve-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderId = this.getAttribute('data-order-id');
                    const form = document.getElementById('approveForm');
                    form.setAttribute('action', `/order-management/approve/${orderId}`);
                    const modal = new bootstrap.Modal('#approveModal');
                    modal.show();
                });
            });

            // Delete Button
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderId = this.getAttribute('data-order-id');
                    const form = document.getElementById('rejectForm');
                    form.setAttribute('action', `/orders/reject/${orderId}`);
                    const modal = new bootstrap.Modal('#deleteModal');
                    modal.show();
                });
            });

            document.querySelectorAll('.delete-order-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderId = this.getAttribute('data-order-id');
                    const form = document.getElementById('deleteForm');
                    form.setAttribute('action', "{{ url('orders/destroy') }}/" + orderId);
                    const modal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
                    modal.show();
                });
            });

            // Change Status
            document.querySelectorAll('.change-status-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderId = this.getAttribute('data-order-id');
                    const form = document.getElementById('changeStatusForm');
                    form.setAttribute('action', `/order-management/change-status/${orderId}`);
                    const modal = new bootstrap.Modal('#changeStatusModal');
                    modal.show();
                });
            });

            // View Allocations
            document.querySelectorAll('.view-allocations-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderId = this.getAttribute('data-order-id');
                    const orderNumber = this.getAttribute('data-order-number');
                    document.getElementById('orderNumberPlaceholder').textContent = orderNumber;
                    document.getElementById('allocationsLoader').style.display = 'block';
                    document.getElementById('allocationsContent').style.display = 'none';
                    fetchAllocations(orderId);
                    const modal = new bootstrap.Modal('#allocationsModal');
                    modal.show();
                });
            });

            // Fetch Allocations
            function fetchAllocations(orderId) {
                fetch(`/order-management/allocations/${orderId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(data => {
                    const tbody = document.getElementById('allocationsTableBody');
                    tbody.innerHTML = '';
                    if (!data.allocations.length) {
                        tbody.innerHTML = '<tr><td colspan="10" class="text-center">No allocations found.</td></tr>';
                    } else {
                        data.allocations.forEach((a, i) => {
                            tbody.insertAdjacentHTML('beforeend', `
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${a.allocated_to_type.charAt(0).toUpperCase() + a.allocated_to_type.slice(1)} - ${a.allocated_to_name || 'N/A'}</td>
                                    <td>${a.qty}</td>
                                    <td>${formatIndianCurrency(a.basic_price)}</td>
                                    <td>${formatIndianCurrency(a.agreed_basic_price)}</td>
                                    <td>${a.token_amount ? formatIndianCurrency(a.token_amount) : '₹0.00'}</td>
                                    <td>${a.payment_terms || '—'}</td>
                                    <td>${a.dispatched_qty}</td>
                                    <td>${a.remaining_qty}</td>
                                    <td>${a.remarks || '—'}</td>
                                </tr>`);
                        });
                    }
                    document.getElementById('allocationsLoader').style.display = 'none';
                    document.getElementById('allocationsContent').style.display = 'block';
                })
                .catch(err => {
                    document.getElementById('allocationsTableBody').innerHTML = '<tr><td colspan="10" class="text-danger">Failed to load.</td></tr>';
                    document.getElementById('allocationsLoader').style.display = 'none';
                    document.getElementById('allocationsContent').style.display = 'block';
                });
            }

            // Select2 + Form Submit
            const dealers = @json($dealers);
            const distributors = @json($distributors);

            $(document).ready(function() {
                $('#party_id').select2({
                    placeholder: 'Search Dealer/Distributor',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#orderModal')
                });

                $('#order_type').on('change', function() {
                    const type = this.value;
                    const select = $('#party_id');
                    select.empty().append('<option value="">Select</option>');
                    const data = type === 'dealer' ? dealers : type === 'distributor' ? distributors : [];
                    data.forEach(p => select.append(`<option value="${p.id}">${p.name}</option>`));
                    select.select2({ placeholder: 'Search Dealer/Distributor', allowClear: true, width: '100%', dropdownParent: $('#orderModal') });
                });

                $('#orderSelectForm').on('submit', function(e) {
                    e.preventDefault();
                    const type = $('#order_type').val();
                    const party = $('#party_id').val();
                    if (type && party) {
                        window.location.href = `{{ route('order_management.create') }}?type=${type}&party=${party}`;
                    }
                });

                // Auto-dismiss alerts
                $('.tt').each(function() {
                    setTimeout(() => $(this).fadeOut(300, function() { $(this).remove(); }), 5000);
                });
                $('.close').on('click', function() {
                    $(this).closest('.tt').fadeOut(300, function() { $(this).remove(); });
                });
            });

            // Alert Modal
            function showAlertModal(message, title = 'Alert', type = 'info') {
                $('#alertModalLabel').text(title);
                $('#alertModalBody').html(message);
                const header = $('#alertModal .modal-header');
                header.removeClass('bg-success bg-danger bg-warning bg-primary').addClass({
                    success: 'bg-success',
                    danger: 'bg-danger',
                    warning: 'bg-warning'
                }[type] || 'bg-primary');
                const modal = new bootstrap.Modal('#alertModal');
                modal.show();
            }

            @if (Session::has('error')) showAlertModal("{{ Session::get('error') }}", 'Error', 'danger'); @endif
            @if ($errors->any())
                let err = ''; @foreach ($errors->all() as $e) err += `{{ $e }}<br>`; @endforeach
                showAlertModal(err, 'Validation Errors', 'danger');
            @endif
        </script>
    @endpush

    @push('styles')
        <style>
            /* [ALL YOUR EXISTING STYLES — UNCHANGED] */
            .order-table thead tr { background: linear-gradient(to right, #4f46e5, #6366f1); color: white; }
            .order-table thead th { padding: 14px 10px; text-transform: uppercase; font-size: 14px; border: none; }
            .order-table tbody tr { background: #f9fafb; transition: all 0.2s ease; }
            .order-table tbody tr:hover { background: #e0f2fe; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
            .order-table td { padding: 12px 10px; vertical-align: middle; font-size: 15px; border: none; }
            .action-btn { background: #6366f1; color: white; border-radius: 50%; width: 38px; height: 38px; display: flex; justify-content: center; align-items: center; border: none; transition: 0.3s; }
            .action-btn:hover { background: #4f46e5; transform: scale(1.1); }
            .dropdown-menu.animated-dropdown { animation: fadeInScale 0.25s ease-in-out; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.15); padding: 8px 0; min-width: 180px; }
            .dropdown-menu.animated-dropdown .dropdown-item { display: flex; align-items: center; font-size: 15px; padding: 10px 18px; color: #333; transition: 0.2s ease-in-out; }
            .dropdown-menu.animated-dropdown .dropdown-item:hover { background: #f1f5f9; transform: translateX(5px); }
            @keyframes fadeInScale { 0% { opacity: 0; transform: scale(0.95) translateY(-10px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
            .custom-btn-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500; transition: all 0.3s ease-in-out; }
            .custom-btn-primary:hover { background: linear-gradient(135deg, #2563eb, #60a5fa); transform: translateY(-2px); color: #fff; }
            .view-allocations-btn { font-size: 14px; font-weight: 500; border-radius: 20px; padding: 6px 14px; transition: all 0.2s ease-in-out; }
            .view-allocations-btn:hover { background-color: #0ea5e9; color: white; transform: scale(1.05); }
            #alertModal .modal-content, #deleteModal .modal-content, #changeStatusModal .modal-content, #allocationsModal .modal-content, #orderModal .modal-content { border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
            .select2-container--default .select2-selection--single { border: 1px solid #ced4da; border-radius: 8px; height: 38px; padding: 5px; }
            .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; }
            .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
            .tt { position: relative; max-width: 600px; margin: 1rem 1rem 1rem auto; padding: 1rem 1.5rem; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; opacity: 0; transform: translateY(-20px); transition: opacity 0.3s ease, transform 0.3s ease; overflow: hidden; }
            .tt.active { opacity: 1; transform: translateY(0); display: flex; }
            .pg { position: absolute; bottom: 0; left: 0; height: 4px; width: 100%; background: #007bff; display: none; }
            .pg.active { display: block; animation: progress 5s linear forwards; }
            @keyframes progress { 0% { width: 100%; } 100% { width: 0; } }
        </style>
    @endpush

    @push('styles')
        <style>
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

            .view-allocations-btn {
                font-size: 14px;
                font-weight: 500;
                border-radius: 20px;
                padding: 6px 14px;
                transition: all 0.2s ease-in-out;
            }

            .view-allocations-btn:hover {
                background-color: #0ea5e9;
                color: white;
                transform: scale(1.05);
            }

            #alertModal .modal-content,
            #deleteModal .modal-content,
            #changeStatusModal .modal-content,
            #allocationsModal .modal-content,
            #orderModal .modal-content {
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }

            #alertModal .modal-body,
            #deleteModal .modal-body,
            #changeStatusModal .modal-body,
            #allocationsModal .modal-body,
            #orderModal .modal-body {
                font-size: 16px;
                line-height: 1.6;
                padding: 1.5rem;
            }

            #alertModal ul {
                padding-left: 1.2rem;
            }

            /* Select2 Styles */
            .select2-container--default .select2-selection--single {
                border: 1px solid #ced4da;
                border-radius: 8px;
                height: 38px;
                padding: 5px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 28px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }

            .select2-container--default .select2-search--dropdown .select2-search__field {
                border-radius: 6px;
                border: 1px solid #ced4da;
                padding: 6px;
            }

            .select2-container--default .select2-results__option {
                padding: 8px 12px;
                font-size: 15px;
            }

            .select2-container--default .select2-results__option--highlighted {
                background-color: #e0f2fe;
                color: #333;
            }

            /* Allocations Modal Styles */
            #allocationsModal .modal-dialog {
                max-width: 90%;
                width: 1200px;
            }

            #allocationsModal .modal-content {
                overflow-x: auto;
                border: none;
            }

            #allocationsModal .modal-header {
                background-color: #343a40;
                color: white;
                border-radius: 12px 12px 0 0;
            }

            #allocationsModal .modal-title {
                font-size: 1.25rem;
                font-weight: 600;
            }

            #allocationsModal .table {
                width: 100%;
                table-layout: fixed;
            }

            #allocationsModal .table th,
            #allocationsModal .table td {
                word-wrap: break-word;
                white-space: normal;
                text-align: center;
                vertical-align: middle;
                font-size: 15px;
                padding: 12px;
                border: 1px solid #dee2e6;
            }

            #allocationsModal .table-dark th {
                background-color: #343a40;
                color: white;
                font-weight: 600;
            }

            #allocationsModal .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f8f9fa;
            }

            #allocationsModal .table-hover tbody tr:hover {
                background-color: #e9ecef;
            }

            @media (max-width: 1200px) {
                #allocationsModal .modal-dialog {
                    max-width: 80%;
                    width: 1000px;
                }
            }

            /* Alert Styles (Matched to Dispatch Index with Right Alignment and Fixed Progress Bar) */
            .tt {
                position: relative;
                max-width: 600px;
                margin: 1rem 1rem 1rem auto;
                padding: 1rem 1.5rem;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                justify-content: space-between;
                opacity: 0;
                transform: translateY(-20px);
                transition: opacity 0.3s ease, transform 0.3s ease;
                overflow: hidden;
            }

            .tt.active {
                opacity: 1;
                transform: translateY(0);
                display: flex;
            }

            .tt-content {
                display: flex;
                align-items: center;
                gap: 1rem;
                flex: 1;
            }

            .tt .check,
            .tt .error {
                font-size: 1.5rem;
                color: #333;
            }

            .tt .message {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .tt .text-1 {
                font-size: 1.1rem;
                font-weight: 600;
                text-transform: uppercase;
                color: #333;
            }

            .tt .text-2 {
                font-size: 1rem;
                line-height: 1.5;
                color: #333;
            }

            .tt .close {
                font-size: 1.2rem;
                cursor: pointer;
                padding: 0.5rem;
                color: #333;
                opacity: 0.7;
                transition: opacity 0.2s;
            }

            .tt .close:hover {
                opacity: 1;
            }

            .pg {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 4px;
                width: 100%;
                background: #007bff;
                display: none;
            }

            .pg.active {
                display: block;
                animation: progress 5s linear forwards;
            }

            @keyframes progress {
                0% { width: 100%; }
                100% { width: 0; }
            }

            @keyframes fadeIn {
                0% { opacity: 0; transform: translateY(-20px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            @keyframes fadeOut {
                0% { opacity: 1; transform: translateY(0); }
                100% { opacity: 0; transform: translateY(-20px); }
            }
        </style>
    @endpush

    @push('head')
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endpush

@endsection
