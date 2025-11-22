@extends('layouts.main')
@section('title', 'Dealers - Singhal')
@section('content')

    <!-- SweetAlert2 CSS -->
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @endpush

    <main id="main" class="main">

        @if ($message = Session::get('success'))
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-check check"></i>
                    <div class="message">
                        <span class="text text-1">Success</span>
                        <span class="text text-2"> {{ $message }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        <div class="dashboard-header pagetitle">
            <h1>Dealers</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Dealers</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-2">
                        <div class="card-body mt-4">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="fw-bold text-dark">
                                    <i class="bi bi-diagram-3-fill me-2 text-primary"></i>Dealer List
                                </h4>

                                <div class="btn-toolbar gap-3">

                                    {{-- Order Limit Approval Request --}}
                                    @can('Dealers-OrderLimitRequests')
                                        <div class="btn-with-badge">
                                            <a href="{{ route('dealers.olRequests') }}"
                                                class="btn custom-btn-secondary btn-icon">
                                                <i class="bi bi-bell-fill"></i>
                                                <span>Order Limit Requests</span>
                                            </a>
                                            @if ($olRequest !== 0)
                                                <span class="notif-badge">{{ $olRequest }}</span>
                                            @endif
                                        </div>
                                    @endcan

                                    <!-- Approval Requests with Badge -->
                                    @can('Dealers-Approve')
                                        <div class="btn-with-badge">
                                            <a href="{{ route('dealers.approvalRequests') }}"
                                                class="btn custom-btn-secondary btn-icon">
                                                <i class="bi bi-bell-fill"></i>
                                                <span>Approval Requests</span>
                                            </a>
                                            @if ($requestCount !== 0)
                                                <span class="notif-badge">{{ $requestCount }}</span>
                                            @endif
                                        </div>
                                    @endcan

                                    @can('Dealers-Create')
                                        <a href="{{ route('dealers.create') }}" class="btn custom-btn-primary btn-icon">
                                            <i class="bi bi-plus-circle-fill"></i>
                                            <span>Add New Dealer</span>
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table distributor-table" id="dealer_table">
                                    <thead>
                                        <tr>
                                            <th class="text__left">#</th>
                                            <th class="text__left">Name</th>
                                            <th class="text__left">Type</th>
                                            <th class="text__left">Code</th>
                                            <th class="text__left">Created At</th>
                                            <th class="text__left">Assigned Distributor</th>
                                            <th class="text__left">Order Limit</th>
                                            <th class="text__left">Allowed Order Limit</th>
                                            <th class="text__left">State</th>
                                            <th class="text__left">Mobile No.</th>
                                            <th class="text__left">Status</th>
                                            <th class="text__left">Created By</th>
                                            <th class="text__left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dealers as $i => $dealer)
                                            <tr>
                                                <td class="text__left">{{ $i+1 }}</td>
                                                <td class="text__left">{{ $dealer->name }}</td>
                                                <td class="text__left">{{ $dealer->type }}</td>
                                                <td class="text__left">{{ $dealer->code }}</td>
                                                <td class="text__left">
                                                    {{ $dealer->approval_time ? $dealer->approval_time->format('j F, Y h:i A') : $dealer->created_at->format('j F, Y h:i A') }}
                                                </td>
                                                <td class="text__left">{{ $dealer->distributor->name ?? 'N/A' }}</td>
                                                <td class="text__left">{{ $dealer->order_limit }} MT</td>
                                                <td class="text__left">{{ $dealer->allowed_order_limit }} MT</td>
                                                <td class="text__left">{{ $dealer->state->state }}</td>
                                                <td class="text__left">{{ $dealer->mobile_no }}</td>
                                                <td class="text__left">
                                                    <span class="badge bg-{{ $dealer->status == 'Active' ? 'success' : 'danger' }}">
                                                        {{ $dealer->status }}
                                                    </span>
                                                </td>
                                                <td class="text__left">{{ $dealer->created_by }}</td>
                                                <td class="text__left">
                                                    <div class="dropdown">
                                                        <button class="btn action-btn dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu animated-dropdown">
                                                            @can('Dealers-View')
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('dealers.show', $dealer->id) }}">
                                                                        <i class="fa fa-eye me-2 text-primary"></i>View
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('Dealers-InActive')
                                                                @if ($dealer->status != 'Inactive')
                                                                    <li>
                                                                        <a class="dropdown-item text-danger mark-dealer-inactive"
                                                                            href="#" data-id="{{ $dealer->id }}"
                                                                            data-name="{{ $dealer->name }}">
                                                                            <i class="fa fa-ban me-2 text-danger"></i>Inactivate
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endcan

                                                            @can('Dealers-Active')
                                                                @if ($dealer->status == 'Inactive')
                                                                    <li>
                                                                        <a class="dropdown-item text-success mark-dealer-active"
                                                                            data-id="{{ $dealer->id }}"
                                                                            data-name="{{ $dealer->name }}" href="#">
                                                                            <i class="fa-solid fa-square-check text-success me-2"></i>Activate
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endcan

                                                            @can('Dealers-OrderLimitChange')
                                                                @if ($dealer->status != 'Inactive')
                                                                    <li>
                                                                        <a class="dropdown-item order-limit-btn" href="#"
                                                                            data-id="{{ $dealer->id }}"
                                                                            data-name="{{ $dealer->name }}"
                                                                            data-current="{{ $dealer->order_limit }}"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#orderLimitModal">
                                                                            <i class="fa-solid fa-arrows-spin text-info me-2"></i>Order Limit Change
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

        <!-- Order Limit Modal -->
        <div class="modal fade" id="orderLimitModal" tabindex="-1" aria-labelledby="orderLimitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('dealers.orderLimitRequest') }}" method="POST">
                    @csrf
                    <input type="hidden" name="submission_token" value="{{ uniqid() }}">
                    <input type="hidden" name="dealer_id" id="modal_dealer_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Request Order Limit Change</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="modal_dealer_name" class="form-label">Dealer Name</label>
                                <input type="text" id="modal_dealer_name" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="modal_current_limit" class="form-label">Current Order Limit (MT)</label>
                                <input type="text" id="modal_current_limit" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="desired_order_limit" class="form-label">Desired Order Limit (MT)</label>
                                <input type="number" name="desired_order_limit" id="desired_order_limit" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dealer Inactivate Modal -->
        <div class="modal fade" id="dealerInactivateModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('dealer.inactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="dealer_inactive_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Dealer Inactivation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="dealer_inactivate_message">
                            <!-- Message injected via JS -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Inactivate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dealer Activate Modal -->
        <div class="modal fade" id="dealerActivateModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('dealer.activate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="dealer_active_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Dealer Activation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="dealer_activate_message">
                            <!-- Message from JS -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Yes, Activate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reusable Alert Modal (for session errors) -->
        <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="alertModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- SweetAlert2 JS -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

    @push('scripts')
        <script>
            // ---- REUSABLE ALERT MODAL -------------------------------------------------
            function showAlertModal(message, title = 'Alert', type = 'info') {
                const modalTitle = document.getElementById('alertModalLabel');
                const modalBody   = document.getElementById('alertModalBody');
                const modalHeader = document.querySelector('#alertModal .modal-header');

                modalTitle.textContent = title;
                modalBody.innerHTML   = message;

                modalHeader.className = 'modal-header text-white';
                switch (type) {
                    case 'success': modalHeader.classList.add('bg-success'); break;
                    case 'danger' : modalHeader.classList.add('bg-danger');  break;
                    case 'warning': modalHeader.classList.add('bg-warning', 'text-dark'); break;
                    default       : modalHeader.classList.add('bg-primary'); break;
                }

                new bootstrap.Modal(document.getElementById('alertModal')).show();
            }

            // Show session errors / validation errors on page load
            document.addEventListener('DOMContentLoaded', () => {
                @if (Session::has('error'))
                    showAlertModal("{{ Session::get('error') }}", 'Error', 'danger');
                @endif

                @if ($errors->any())
                    let msgs = `<ul class="mb-0">`;
                    @foreach ($errors->all() as $e) msgs += `<li>{{ $e }}</li>`; @endforeach
                    msgs += `</ul>`;
                    showAlertModal(msgs, 'Validation Errors', 'danger');
                @endif
            });
        </script>
    @endpush

    @push('scripts')
        <script>
            // Order Limit Modal Population
            document.querySelectorAll('.order-limit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const dealerId = button.getAttribute('data-id');
                    const dealerName = button.getAttribute('data-name');
                    const currentLimit = button.getAttribute('data-current');

                    document.getElementById('modal_dealer_id').value = dealerId;
                    document.getElementById('modal_dealer_name').value = dealerName;
                    document.getElementById('modal_current_limit').value = currentLimit;
                });
            });

            // === INACTIVATE DEALER WITH SWEETALERT BLOCK ===
            document.querySelectorAll('.mark-dealer-inactive').forEach(button => {
                button.addEventListener('click', async (e) => {
                    e.preventDefault();

                    const dealerId   = button.dataset.id;
                    const dealerName = button.dataset.name;

                    try {
                        const resp   = await fetch(`/dealer/check-inactivation/${dealerId}`);
                        const result = await resp.json();

                        // 1. BLOCKED: Has pending orders
                        if (result.blocked) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Inactivate Dealer',
                                html: `<strong>${dealerName}</strong><br>${result.message}`,
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn btn-danger' },
                                buttonsStyling: false
                            });
                            return;
                        }

                        // 2. CONFIRMATION REQUIRED: Part of a distributor team
                        if (result.confirmationRequired) {
                            document.getElementById('dealer_inactive_id').value = dealerId;
                            document.getElementById('dealer_inactivate_message').innerHTML = result.message;
                            new bootstrap.Modal(document.getElementById('dealerInactivateModal')).show();
                            return;
                        }

                        // 3. NO ISSUES: Simple confirmation
                        document.getElementById('dealer_inactive_id').value = dealerId;
                        document.getElementById('dealer_inactivate_message').innerHTML =
                            `Are you sure you want to mark <strong>${dealerName}</strong> as inactive?`;

                        new bootstrap.Modal(document.getElementById('dealerInactivateModal')).show();

                    } catch (err) {
                        console.error('Error checking inactivation:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to check dealer status. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // === ACTIVATE DEALER ===
            document.querySelectorAll('.mark-dealer-active').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const dealerId   = button.dataset.id;
                    const dealerName = button.dataset.name;

                    document.getElementById('dealer_active_id').value = dealerId;
                    document.getElementById('dealer_activate_message').innerHTML =
                        `Are you sure you want to mark <strong>${dealerName}</strong> as active?`;

                    new bootstrap.Modal(document.getElementById('dealerActivateModal')).show();
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .distributor-table {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                border-collapse: separate;
                border-spacing: 0 10px;
                width: 100%;
                background: #fff;
            }

            .distributor-table thead tr {
                background: linear-gradient(to right, #4f46e5, #6366f1);
                color: white;
            }

            .distributor-table thead th {
                padding: 14px 10px;
                text-transform: uppercase;
                font-size: 14px;
                letter-spacing: 0.5px;
                border: none;
            }

            .distributor-table tbody tr {
                background: #f9fafb;
                transition: all 0.2s ease;
            }

            .distributor-table tbody tr:hover {
                background: #e0f2fe;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            }

            .distributor-table td {
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
                0% { opacity: 0; transform: scale(0.95) translateY(-10px); }
                100% { opacity: 1; transform: scale(1) translateY(0); }
            }

            .custom-btn-primary {
                background: linear-gradient(135deg, #3b82f6, #2563eb);
                color: #fff;
                border: none;
                padding: 10px 16px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
                transition: all 0.3s ease-in-out;
            }

            .custom-btn-primary:hover {
                background: linear-gradient(135deg, #2563eb, #60a5fa);
                transform: translateY(-2px);
            }

            .custom-btn-secondary {
                background: linear-gradient(135deg, #6b7280, #9ca3af);
                color: white;
                border: none;
                box-shadow: 0 4px 10px rgba(107, 114, 128, 0.3);
                transition: all 0.3s ease-in-out;
            }

            .custom-btn-secondary:hover {
                background: linear-gradient(135deg, #4b5563, #9ca3af);
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(107, 114, 128, 0.4);
            }

            .btn-icon {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 500;
            }

            .btn-with-badge {
                position: relative;
                display: inline-block;
            }

            .notif-badge {
                position: absolute;
                top: -6px;
                right: -10px;
                background-color: #dc3545;
                color: white;
                font-size: 0.7rem;
                padding: 2px 6px;
                border-radius: 20px;
                font-weight: bold;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                z-index: 1;
                transition: 0.3s ease-in-out;
            }

            .notif-badge:hover {
                transform: scale(1.1);
            }
        </style>
    @endpush

@endsection
