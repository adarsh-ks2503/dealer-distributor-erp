@extends('layouts.main')
@section('title', 'Distributor Team - Singhal')
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

    @if ($message = Session::get('error'))
        <div class="tt active">
            <div class="tt-content">
                <i class="fas fa-solid fa-xmark-circle error"></i>
                <div class="message">
                    <span class="text text-1">Error</span>
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
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </span>
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="pg active"></div>
        </div>
    @endif

    <div class="dashboard-header pagetitle">
        <h1>Distributor Team</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Distributor Team</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-2">
                    <div class="card-body mt-4">
                        @can('DistributorsTeam-Create')
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center"></div>
                                <a href="{{ route('distributor_team.create') }}" class="btn custom-btn-primary">
                                    <span>Add New Team</span>
                                </a>
                            </div>
                        @endcan

                        <div class="table-responsive">
                            <table class="table distributor-table text-center align-middle" id="distributor_team_table">
                                <thead>
                                    <tr>
                                        <th class="text__left">#</th>
                                        <th class="text__left">Distributor Name</th>
                                        <th class="text__left">Distributor Code</th>
                                        <th class="text__left">State</th>
                                        <th class="text__left">Mobile No.</th>
                                        <th class="text__left">Assigned Dealers</th>
                                        <th class="text__left">Total Order Limit (MT)</th>
                                        <th class="text__left">Ordered Qty (MT)</th>
                                        <th class="text__left">Status</th>
                                        <th class="text__left">Created At</th>
                                        <th class="text__left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teams as $i => $team)
                                        <tr>
                                            <td class="text__left">{{ $i + 1 }}</td>
                                            <td class="text__left">{{ $team->distributor->name }}</td>
                                            <td class="text__left">{{ $team->distributor->code }}</td>
                                            <td class="text__left">{{ $team->distributor->state->state ?? 'N/A' }}</td>
                                            <td class="text__left">{{ $team->distributor->mobile_no }}</td>
                                            <td class="text__left">
                                                <button type="button" class="btn btn-outline-info btn-sm view-dealers-btn"
                                                        data-bs-toggle="modal" data-bs-target="#dealersModal"
                                                        data-team-id="{{ $team->id }}"
                                                        data-distributor-name="{{ $team->distributor->name }}">
                                                    {{ $team->active_dealer_count }} Dealers
                                                </button>
                                            </td>
                                            <td class="text__left">{{ $team->active_total_order_limit }}</td>
                                            <td class="text__left">{{ $team->ordered_quantity ?? 0 }}</td>
                                            <td class="text__left">
                                                <span class="badge bg-{{ $team->status === 'Active' ? 'success' : 'secondary' }}">
                                                    {{ $team->status }}
                                                </span>
                                            </td>
                                            <td class="text__left">{{ $team->created_at->format('j F, Y h:i A') }}</td>
                                            <td class="text__left">
                                                <div class="dropdown">
                                                    <button class="btn action-btn dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu animated-dropdown">
                                                        @can('DistributorsTeam-View')
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('distributor_team.show', $team->id) }}"><i
                                                                        class="fa fa-eye me-2 text-primary"></i>View</a>
                                                            </li>
                                                        @endcan
                                                        @can('DistributorsTeam-Edit')
                                                            @if ($team->status != 'Suspended')
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('distributor_team.edit', $team->id) }}"><i
                                                                            class="fa fa-edit me-2 text-warning"></i>Edit</a>
                                                                </li>
                                                            @endcan
                                                            @can('DistributorsTeam-Suspend')
                                                                <li>
                                                                    <a class="dropdown-item text-danger suspend-team-btn"
                                                                        href="#" data-team-id="{{ $team->id }}">
                                                                        <i class="fa fa-ban me-2 text-danger"></i>Suspend Team
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        @endif
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

    <!-- Dealers Modal -->
    <div class="modal fade" id="dealersModal" tabindex="-1" aria-labelledby="dealersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="dealersModalLabel">Current Dealers - <span id="distributorNamePlaceholder"></span></h5>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="dealersLoader" class="text-center py-4" style="display:none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading dealers...</p>
                    </div>
                    <div id="dealersContent" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered text-center" id="dealersTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Dealer ID</th>
                                        <th>Dealer Name</th>
                                        <th>State</th>
                                        <th>Order Limit (MT)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="dealersTableBody">
                                    <!-- Dynamically populated -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspend Team Modal -->
    <div class="modal fade" id="suspendTeamModal" tabindex="-1" aria-labelledby="suspendTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="suspendTeamForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="suspendTeamModalLabel">Suspend Team</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to suspend this team? All associated dealers will become free.
                        Team suspended once cannot be reactivated. You will need to create a new team if required.
                        <br><br>
                        Please confirm your action.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Suspend</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="alertModalLabel">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease-in-out;
            font-weight: 500;
        }

        .custom-btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            transform: translateY(-2px);
            color: #fff;
        }

        .view-dealers-btn {
            font-size: 14px;
            font-weight: 500;
            border-radius: 20px;
            padding: 6px 14px;
            transition: all 0.2s ease-in-out;
        }

        .view-dealers-btn:hover {
            background-color: #0ea5e9;
            color: white;
            transform: scale(1.05);
        }

        #dealersModal .modal-dialog {
            max-width: 90%;
            width: 1200px;
        }

        #dealersModal .modal-content {
            overflow-x: auto;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        #dealersModal .modal-header {
            background-color: #343a40;
            color: white;
            border-radius: 12px 12px 0 0;
        }

        #dealersModal .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        #dealersModal .modal-body {
            padding: 1.5rem;
        }

        #dealersModal .table {
            width: 100%;
            table-layout: fixed;
        }

        #dealersModal .table th,
        #dealersModal .table td {
            word-wrap: break-word;
            white-space: normal;
            text-align: center;
            vertical-align: middle;
            font-size: 15px;
            padding: 12px;
            border: 1px solid #dee2e6;
        }

        #dealersModal .table-dark th {
            background-color: #343a40;
            color: white;
            font-weight: 600;
        }

        #dealersModal .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        #dealersModal .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        @media (max-width: 1200px) {
            #dealersModal .modal-dialog {
                max-width: 80%;
                width: 1000px;
            }
        }

        #alertModal .modal-content,
        #suspendTeamModal .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        #alertModal .modal-body,
        #suspendTeamModal .modal-body {
            font-size: 16px;
            line-height: 1.6;
            padding: 1.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Suspend Team Button Logic
            const suspendButtons = document.querySelectorAll('.suspend-team-btn');
            const suspendForm = document.getElementById('suspendTeamForm');
            const suspendModal = new bootstrap.Modal(document.getElementById('suspendTeamModal'), {
                backdrop: 'static',
                keyboard: true
            });

            suspendButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const teamId = this.getAttribute('data-team-id');
                    suspendForm.setAttribute('action', `/distributor-team/${teamId}/suspend`);
                    suspendModal.show();
                });
            });

            // Dealers Button Logic
            const dealerButtons = document.querySelectorAll('.view-dealers-btn');
            const dealersModal = new bootstrap.Modal(document.getElementById('dealersModal'), {
                backdrop: 'static',
                keyboard: true
            });
            const loader = document.getElementById('dealersLoader');
            const content = document.getElementById('dealersContent');
            const tbody = document.getElementById('dealersTableBody');

            dealerButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const teamId = this.getAttribute('data-team-id');
                    const distributorName = this.getAttribute('data-distributor-name');
                    document.getElementById('distributorNamePlaceholder').textContent = distributorName;
                    loader.style.display = 'block';
                    content.style.display = 'none';
                    tbody.innerHTML = '';

                    fetch(`/distributor-team/${teamId}/dealers`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const dealers = Array.isArray(data.dealers) ? data.dealers : [];
                        tbody.innerHTML = '';

                        if (dealers.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No active dealers.</td></tr>';
                        } else {
                            dealers.forEach((dealer, index) => {
                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${dealer.code || 'N/A'}</td>
                                        <td>${dealer.name}</td>
                                        <td>${dealer.state?.state || 'N/A'}</td>
                                        <td>${dealer.order_limit} MT</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                    </tr>`;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });
                        }
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching dealers:', error);
                        tbody.innerHTML = '<tr><td colspan="6" class="text-danger text-center">Failed to load dealers.</td></tr>';
                        loader.style.display = 'none';
                        content.style.display = 'block';
                        showAlertModal('Failed to load dealers: ' + error.message, 'Error', 'danger');
                    });
                });
            });

            // Handle Modal Closing to Remove Backdrop
            document.getElementById('dealersModal').addEventListener('hidden.bs.modal', function () {
                document.body.classList.remove('modal-open');
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                this.classList.remove('show');
                document.getElementById('dealersTableBody').innerHTML = '';
                document.getElementById('dealersLoader').style.display = 'block';
                document.getElementById('dealersContent').style.display = 'none';
            });

            // Alert Modal Logic
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

            function showAlertModal(message, title = 'Alert', type = 'info') {
                const modalTitle = document.getElementById('alertModalLabel');
                const modalBody = document.getElementById('alertModalBody');
                const modalHeader = document.querySelector('#alertModal .modal-header');

                modalTitle.textContent = title;
                modalBody.innerHTML = message;
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
                        break;
                    default:
                        modalHeader.classList.add('bg-primary');
                }
                const modal = new bootstrap.Modal(document.getElementById('alertModal'), {
                    backdrop: 'static',
                    keyboard: true
                });
                modal.show();
            }

            // Auto-dismiss toast notifications
            const toasts = document.querySelectorAll('.tt.active');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.remove('active');
                }, 5000);
            });
        });
    </script>
@endpush

@push('head')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@endsection
