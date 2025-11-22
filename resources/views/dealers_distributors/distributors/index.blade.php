@extends('layouts.main')
@section('title', 'Distributors - Singhal')
@section('content')

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

        {{-- @if ($message = Session::get('error'))
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-check check"></i>
                    <div class="message">
                        <span class="text text-1">Error</span>
                        <span class="text text-2"> {{ $message }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif --}}

        {{-- @if ($errors->any())
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-xmark-circle error"></i>
                    <div class="message">
                        <span class="text text-1">Error</span>
                        <span class="text text-2">{{ $errors }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif --}}

        <div class="dashboard-header pagetitle">
            <h1>Distributors</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Distributors</li>
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
                                    <i class="bi bi-diagram-3-fill me-2 text-primary"></i>Distributor List
                                </h4>
                                <div class="btn-toolbar gap-3">

                                    {{-- Order Limit Approval Request --}}
                                    @can('Distributors-OrderLimitRequests')

                                        <div class="btn-with-badge">
                                            <a href="{{ route('distributors.olRequests') }}"
                                                class="btn custom-btn-secondary btn-icon">
                                                <i class="bi bi-bell-fill"></i>
                                                <span>Order Limit Requests</span>
                                            </a>
                                            @if ($olRequest !== 0)
                                                <span class="notif-badge">{{ $olRequest }}</span>
                                                <!-- Replace with dynamic count -->
                                            @endif
                                        </div>
                                    @endcan
                                    @can('Distributors-Create')
                                        <a href="{{ route('distributors.create') }}" class="btn custom-btn-primary btn-icon">
                                            <i class="bi bi-plus-circle-fill"></i>
                                            <span>Add New Distributor</span>
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table distributor-table" id="distributor_table">
                                    <thead>
                                        <tr>
                                            <th class="text__left">#</th>
                                            <th class="text__left">Name</th>
                                            <th class="text__left">Code</th>
                                            <th class="text__left">Created At</th>
                                            <th class="text__left">Order Limit</th>
                                            <th class="text__left">Allowed Order Limit</th>
                                            <th class="text__left">Individual Allowed Order Limit</th>
                                            <th class="text__left">State</th>
                                            <th class="text__left">Mobile No.</th>
                                            <th class="text__left">Status</th>
                                            <th class="text__left">Created By</th>
                                            <th class="text__left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($distributors as $i => $distributor)
                                            <tr>
                                                <td class="text_left">{{ $i+1 }}</td>
                                                <td class="text_left">{{ $distributor->name }}</td>
                                                <td class="text_left">{{ $distributor->code }}</td>
                                                <td class="text_left">{{ $distributor->created_at->format('j F, Y h:i A') }}</td>
                                                <td class="text_left">{{ $distributor->order_limit }} MT</td>
                                                <td class="text_left">{{ $distributor->allowed_order_limit }} MT</td>
                                                <td class="text_left">{{ $distributor->individual_allowed_order_limit }} MT</td>
                                                <td class="text_left">{{ $distributor->state->state }}</td>
                                                <td class="text_left">{{ $distributor->mobile_no }}</td>
                                                <td class="text_left"><span
                                                        class="badge bg-{{ $distributor->status == 'Active' ? 'success' : 'danger' }}">{{ $distributor->status }}</span>
                                                </td>
                                                <td class="text_left">{{ $distributor->created_by }}</td>
                                                <td class="text_left">
                                                    <div class="dropdown">
                                                        <button class="btn action-btn dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu animated-dropdown">
                                                            @can('Distributors-View')
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('distributors.show', $distributor->id) }}"><i
                                                                            class="fa fa-eye me-2 text-primary"></i>View</a>
                                                                </li>
                                                            @endcan
                                                            @can('Distributors-Edit')
                                                                @if ($distributor->status != 'Inactive')
                                                                    <li><a class="dropdown-item"
                                                                            href="{{ route('distributors.edit', $distributor->id) }}"><i
                                                                                class="fa fa-edit me-2 text-warning"></i>Edit</a>
                                                                    </li>
                                                                @endif
                                                            @endcan
                                                            @can('Distributors-InActive')
                                                                @if ($distributor->status != 'Inactive')
                                                                    <li>
                                                                        <a class="dropdown-item text-danger mark-distributor-inactive"
                                                                            data-id="{{ $distributor->id }}"
                                                                            data-name="{{ $distributor->name }}"
                                                                            href="#">
                                                                            <i class="fa fa-ban me-2 text-danger">
                                                                            </i>Inactivate
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endcan
                                                            @can('Distributors-Active')
                                                                <!-- $distributor-?status == ?Inactiv-->

                                                                @if ($distributor->status == 'Inactive')
                                                                    <li>
                                                                        <a class="dropdown-item text-success mark-distributor-active"
                                                                            data-id="{{ $distributor->id }}"
                                                                            data-name="{{ $distributor->name }}"
                                                                            href="#">
                                                                            <i
                                                                                class="fa-solid fa-square-check text-success"></i>
                                                                            </i>Activate
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endcan
                                                            @can('Distributors-OrderLimitChange')
                                                                @if ($distributor->status != 'Inactive')
                                                                    <li><a class="dropdown-item order-limit-btn" href="#"
                                                                            data-id="{{ $distributor->id }}"
                                                                            data-name="{{ $distributor->name }}"
                                                                            data-current="{{ $distributor->order_limit }}"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#orderLimitModal">
                                                                            <i
                                                                                class="fa-solid fa-arrows-spin text-info"></i>Order
                                                                            Limit Change
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

        <!-- Alert Modal -->
        <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="alertModalLabel">Alert</h5>
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

        <!-- Order Limit Modal -->
        <div class="modal fade" id="orderLimitModal" tabindex="-1" aria-labelledby="orderLimitModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('distributors.orderLimitRequest') }}" method="POST">
                    @csrf
                    <input type="hidden" name="submission_token" value="{{ uniqid() }}">
                    <input type="hidden" name="distributor_id" id="modal_dealer_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Request Order Limit Change</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="modal_dealer_name" class="form-label">Distributor Name</label>
                                <input type="text" id="modal_dealer_name" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="modal_current_limit" class="form-label">Current Order Limit(MT)</label>
                                <input type="text" id="modal_current_limit" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="desired_order_limit" class="form-label">Desired Order Limit(MT)</label>
                                <input type="number" name="desired_order_limit" id="desired_order_limit"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="desired_order_limit" class="form-label">Remarks</label>
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

        <!-- Distributor Inactivate Modal -->
        <div class="modal fade" id="distributorInactivateModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('distributor.inactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="distributor_inactive_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Distributor Inactivation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="distributor_inactivate_message">
                            <!-- Message from JS -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Inactivate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Distributor Inactivate Modal -->
        <div class="modal fade" id="distributorActivateModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('distributor.activate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="distributor_active_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Distributor Activation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="distributor_activate_message">
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


    </main>

    {{-- @push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Laravel session messages
        @if (Session::has('error'))
            showAlertModal("{{ Session::get('error') }}", 'Error', 'danger');
        @endif

        // @if (Session::has('success'))
        //     showAlertModal("{{ Session::get('success') }}", 'Success', 'success');
        // @endif

        @if ($errors->any())
            let errorMessages = `<ul class="mb-0">`;
            @foreach ($errors->all() as $error)
                errorMessages += `<li>{{ $error }}</li>`;
            @endforeach
            errorMessages += `</ul>`;
            showAlertModal(errorMessages, 'Validation Errors', 'danger');
        @endif

        function showAlertModal(message, title = 'Alert', type = 'info') {
            const modalTitle = document.getElementById('alertModalLabel');
            const modalBody = document.getElementById('alertModalBody');
            const modalHeader = document.querySelector('#alertModal .modal-header');

            modalTitle.textContent = title;
            modalBody.innerHTML = message;

            // Set header color based on type
            modalHeader.className = 'modal-header text-white';
            switch (type) {
                case 'success':
                    modalHeader.classList.add('bg-success');
                    break;
                case 'danger':
                    modalHeader.classList.add('bg-danger');
                    break;
                case 'warning':
                    modalHeader.classList.add('bg-warning');
                    modalHeader.classList.add('text-dark');
                    break;
                default:
                    modalHeader.classList.add('bg-primary');
            }

            const modal = new bootstrap.Modal(document.getElementById('alertModal'));
            modal.show();
        }
    });
</script>
@endpush --}}

    @push('scripts')
        <script>
            document.querySelectorAll('.order-limit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const distributorId = button.getAttribute('data-id');
                    const distributorName = button.getAttribute('data-name');
                    const currentLimit = button.getAttribute('data-current');

                    document.getElementById('modal_dealer_id').value = distributorId;
                    document.getElementById('modal_dealer_name').value = distributorName;
                    document.getElementById('modal_current_limit').value = currentLimit;
                });
            });
        </script>
        <script>
            document.querySelectorAll('.mark-distributor-active').forEach(button => {
                button.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const distributorId = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');

                    // const response = await fetch(`/distributor/check-inactivation/${distributorId}`);
                    // const result = await response.json();

                    // if (result.blocked) {
                    //     showToast('error', result.message);
                    // } else {
                    document.getElementById('distributor_active_id').value = distributorId;
                    document.getElementById('distributor_activate_message').innerHTML =
                        `Are you sure you want to mark <strong>${name}</strong> as active?`;

                    new bootstrap.Modal(document.getElementById('distributorActivateModal')).show();
                    // }
                });

            });


            document.querySelectorAll('.mark-distributor-inactive').forEach(button => {
                button.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const distributorId = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');

                    try {
                        const response = await fetch(`/distributor/check-inactivation/${distributorId}`);
                        const result = await response.json();

                        if (!result.blocked) {
                            document.getElementById('distributor_inactive_id').value = distributorId;
                            document.getElementById('distributor_inactivate_message').innerHTML =
                                `Are you sure you want to mark <strong>${name}</strong> as inactive?`;

                            new bootstrap.Modal(document.getElementById('distributorInactivateModal'))
                                .show();
                        } else {
                            showAlertModal(result.message, 'Blocked', 'danger');
                        }
                    } catch (error) {
                        console.error('Error parsing response:', error);
                        showAlertModal('Something went wrong while checking distributor status.', 'Error',
                            'danger');
                    }
                });
            });
        </script>
    @endpush

    @push('scripts')
        <script>
            // ✅ Define globally so it's accessible everywhere
            function showAlertModal(message, title = 'Alert', type = 'info') {
                const modalTitle = document.getElementById('alertModalLabel');
                const modalBody = document.getElementById('alertModalBody');
                const modalHeader = document.querySelector('#alertModal .modal-header');

                modalTitle.textContent = title;
                modalBody.innerHTML = message;

                // Set header color based on type
                modalHeader.className = 'modal-header text-white';
                switch (type) {
                    case 'success':
                        modalHeader.classList.add('bg-success');
                        break;
                    case 'danger':
                        modalHeader.classList.add('bg-danger');
                        break;
                    case 'warning':
                        modalHeader.classList.add('bg-warning', 'text-dark');
                        break;
                    default:
                        modalHeader.classList.add('bg-primary');
                }

                const modal = new bootstrap.Modal(document.getElementById('alertModal'));
                modal.show();
            }

            document.addEventListener("DOMContentLoaded", function() {
                @if (Session::has('error'))
                    showAlertModal("{{ Session::get('error') }}", 'Error', 'danger');
                @endif

                @if ($errors->any())
                    let errorMessages = `<ul class="mb-0">`;
                    @foreach ($errors->all() as $error)
                        errorMessages += `<li>{{ $error }}</li>`;
                    @endforeach
                    errorMessages += `</ul>`;
                    showAlertModal(errorMessages, 'Validation Errors', 'danger');
                @endif
            });
        </script>
    @endpush


    @push('styles')
        <style>
            .custom-btn-primary {
                background: linear-gradient(135deg, #1d4ed8, #3b82f6);
                color: white;
                border: none;
                box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
                transition: all 0.3s ease-in-out;
            }

            .custom-btn-primary:hover {
                background: linear-gradient(135deg, #2563eb, #60a5fa);
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
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
                gap: 0.5rem;
                font-weight: 500;
                font-size: 0.95rem;
            }

            /* Badge styling */
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
                padding: 10px 16px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
                transition: all 0.3s ease-in-out;
            }

            .custom-btn-primary:hover {
                background: linear-gradient(135deg, #2563eb, #60a5fa);
                transform: translateY(-2px);
            }

            .btn-icon {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 500;
            }
        </style>
    @endpush

    @push('styles')
        <style>
            #alertModal .modal-content {
                border-radius: 10px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }

            #alertModal .modal-body {
                font-size: 16px;
                line-height: 1.6;
            }

            #alertModal ul {
                padding-left: 1.2rem;
            }
        </style>
    @endpush

@endsection
