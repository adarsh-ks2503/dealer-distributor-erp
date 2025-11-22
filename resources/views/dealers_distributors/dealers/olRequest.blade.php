@extends('layouts.main')
@section('title', 'Dealers Order Limit Approval - Singhal')
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
                    <i class="fas fa-solid fa-xmark-circle error"></i>
                    <div class="message">
                        <span class="text text-1">Error</span>
                        <span class="text text-2">{{ $message }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div> --}}
        {{-- @endif --}}

        @if ($errors->any())
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-xmark-circle error"></i>
                    <div class="message">
                        <span class="text text-1">Rejected</span>
                        <ul class="text text-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        <div class="dashboard-header pagetitle">
            <h1>Dealers Order Limit Requests</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dealers</a></li>
                    <li class="breadcrumb-item active">Dealers Order Limit Requests</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">

                <div class="col-lg-12">
                    <div class="card mt-2">
                        <div class="card-body mt-4">

                            <div class="row ">
                                <div class="col-md-6 col-sm-12">
                                    <div class="pd-20">
                                        <h4 class="text-blue h4">Dealers Order Limit Requests</h4>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex justify-content-end ">
                                    <div class="btn-group">
                                        <div>
                                            <a class="btn btn-primary mb-4 mr-3" href="{{ route('dealers.index') }}">
                                                Back
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="display stripe row-border order-column" id="dlr_ol_req" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Dealer Name</th>
                                        <th class="text__left">DEALER CODE</th>
                                        <th class="text__left">CURRENT ORDER LIMIT</th>
                                        <th class="text__left">REQUESTED ORDER LIMIT</th>
                                        <th class="text__left">REQUESTED DATE-TIME</th>
                                        <th class="text__left">REMARKS</th>
                                        <th class="text__left">STATUS</th>
                                        <th class="text__left">STATUS CHANGE REMARK</th>
                                        <th class="text__left">STATUS CHANGE DATE-TIME</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $item)
                                        <tr>
                                            <td>{{ $item->dealer->name }}</td>
                                            <td class="text__left">{{ $item->dealer->code }}</td>
                                            <td class="text__left">{{ $item->order_limit }}</td>
                                            <td class="text__left">{{ $item->desired_order_limit }}</td>
                                            <td class="text__left">{{ $item->created_at->format('j F, Y h:i A') }}</td>
                                            <td class="text__left">{{ $item->remarks }}</td>
                                            <td class="text__left">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-warning',
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                    ];
                                                    $statusClass = $statusClasses[strtolower($item->status)] ?? 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                            </td>
                                            <td class="text__left">{{ $item->status_change_remarks ?? 'N/A' }}</td>
                                            <td class="text__left">{{ $item->updated_at->format('j F, Y h:i A') ?? 'N/A' }}</td>
                                            @if ($item->status === 'Pending')
                                                <td>
                                                    <div class="dropdown">
                                                        <span class="btn btn-sm btn-primary dropdown-toggle" id="actionMenu{{ $item->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-solid fa-list"></i>
                                                        </span>
                                                        <ul class="dropdown-menu custom-dropdown-menu" aria-labelledby="actionMenu{{ $item->id }}">
                                                            <li>
                                                                <button class="dropdown-item approve-btn"
                                                                        data-id="{{ $item->id }}"
                                                                        data-url="{{ route('dealers.olRequestApprove', $item->id) }}">
                                                                    <i class="fa-solid fa-thumbs-up me-2" style="color: #1c84d4;"></i> Approve
                                                                </button>
                                                            </li>
                                                            @if ($item->status === 'Pending')
                                                                <li>
                                                                    <button class="dropdown-item reject-btn"
                                                                            data-id="{{ $item->id }}"
                                                                            data-url="{{ route('dealers.olRequestReject', $item->id) }}">
                                                                        <i class="fa-solid fa-ban me-2" style="color: #ff3c2e;"></i> Reject
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                                @else
                                                    <td>N/A</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

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
                        <h5 class="modal-title" id="alertModalLabel">Warning</h5>
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

    </main><!-- End #main -->

    <!-- Approve Confirmation Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Approval</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="width:50px"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <strong>approve</strong> this order limit updation request?
                        <br>
                        <br>
                        <input type="text"
                                   name="status_change_remarks"
                                   class="form-control custom-input"
                                   placeholder="Status Change Remarks">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Yes, Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Rejection</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="width:50px"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <strong>reject</strong> this order limit updation request?
                        <br>
                        <br>
                        <input type="text"
                                   name="status_change_remarks"
                                   class="form-control custom-input"
                                   placeholder="Status Change Remarks">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Laravel session messages
            @if (Session::has('error'))
                showAlertModal("{{ Session::get('error') }}", 'Rejected ', 'danger');
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
    @endpush

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inactiveButtons = document.querySelectorAll('.open-inactive-modal');
            const form = document.getElementById('inactiveForm');

            inactiveButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const action = this.dataset.action;
                    form.action = action;

                    const modal = new bootstrap.Modal(document.getElementById('confirmInactiveModal'));
                    modal.show();
                });
            });
        });
    </script>

        @push('styles')
            <style>
                table.dataTable td {
                    overflow: visible !important;
                }

                .dropdown-menu {
                    z-index: 9999 !important;
                }
                table.dataTable td {
                position: static !important;
            }
            .dropdown-menu.custom-dropdown-menu {
                    animation: fadeIn 0.25s ease-in-out;
                    border-radius: 8px;
                    padding: 0.3rem 0;
                    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
                    min-width: 160px;
            }

            .dropdown-menu.custom-dropdown-menu .dropdown-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.95rem;
                transition: background-color 0.2s ease, transform 0.15s ease;
                padding: 8px 16px;
            }

            .dropdown-menu.custom-dropdown-menu .dropdown-item:hover {
                background-color: #f8f9fa;
                transform: translateX(4px);
            }

            .dropdown-menu.custom-dropdown-menu .dropdown-item i {
                width: 18px;
            }

            .dropdown-menu.custom-dropdown-menu .dropdown-item.text-danger:hover {
                background-color: #ffe6e6;
            }

            @keyframes fadeIn {
                0% {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            </style>
        @endpush

        @push('styles')
<style>
    #alertModal .modal-content {
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
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


        @push('scripts')
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const approveButtons = document.querySelectorAll('.approve-btn');
                    const rejectButtons = document.querySelectorAll('.reject-btn');

                    const approveForm = document.getElementById('approveForm');
                    const rejectForm = document.getElementById('rejectForm');

                    approveButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            const url = this.dataset.url;
                            approveForm.action = url;

                            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                            modal.show();
                        });
                    });

                    rejectButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            const url = this.dataset.url;
                            rejectForm.action = url;

                            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                            modal.show();
                        });
                    });
                });
            </script>
        @endpush

@endsection
