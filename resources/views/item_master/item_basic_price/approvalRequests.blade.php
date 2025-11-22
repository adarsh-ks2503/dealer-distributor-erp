@extends('layouts.main')
@section('title', 'Item Basic Prices Approval - Singhal')

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
            <h1>Item Basic Prices Approval Page</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Item Master</a></li>
                    <li class="breadcrumb-item">Item Basic Prices Approval Page</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-2">
                        <div class="card-body mt-4">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="pd-20">
                                        <h4 class="text-blue h4">Item Basic Prices Approval Requests</h4>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex justify-content-end">
                                    <div class="btn-group">
                                        <div>
                                            <a class="btn btn-primary mb-4 mx-1" href="{{ route('itemBasicPrice.index') }}">
                                                Back
                                            </a>
                                        </div>
                                        {{-- <div>
                                            <button class="btn btn-success mb-4 mx-1 approve-all-btn" data-bs-toggle="modal" data-bs-target="#approveAllModal">
                                                Approve All
                                            </button>
                                        </div>
                                        <div>
                                            <button class="btn btn-danger mb-4 mx-1 reject-all-btn" data-bs-toggle="modal" data-bs-target="#rejectAllModal">
                                                Reject All
                                            </button>
                                        </div> --}}

                                        <div>
    <button class="btn btn-success mb-4 mx-1 approve-all-btn">
        Approve All
    </button>
</div>
<div>
    <button class="btn btn-danger mb-4 mx-1 reject-all-btn">
        Reject All
    </button>
</div>
                                    </div>
                                </div>
                            </div>

                            <table class="display stripe row-border order-column" id="item_basic_price_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ITEM</th>
                                        <th class="text__left">STATE</th>
                                        <th class="text__left">MARKET BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">DISTRIBUTOR BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">DEALER BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">CREATED AT</th>
                                        <th>REMARKS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $item->itemName->item_name }}</td>
                                            <td class="text__left">{{ $item->stateName?->state }}</td>
                                            <td class="text__left">{{ $item->market_basic_price }}</td>
                                            <td class="text__left">{{ $item->distributor_basic_price }}</td>
                                            <td class="text__left">{{ $item->dealer_basic_price }}</td>
                                            <td class="text__left">{{ $item->created_at->format('j F, Y h:i A') }}</td>
                                            <td>{{ $item->remarks }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <span class="btn btn-sm btn-primary dropdown-toggle" id="actionMenu{{ $item->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa-solid fa-list"></i>
                                                    </span>
                                                    <ul class="dropdown-menu custom-dropdown-menu" aria-labelledby="actionMenu{{ $item->id }}">
                                                        <li>
                                                            <button class="dropdown-item approve-btn"
                                                                    data-id="{{ $item->id }}"
                                                                    data-url="{{ route('itemBasicPrice.approve', $item->id) }}">
                                                                <i class="fa-solid fa-thumbs-up me-2" style="color: #1c84d4;"></i> Approve
                                                            </button>
                                                        </li>
                                                        @if ($item->status === 'Pending')
                                                            <li>
                                                                <button class="dropdown-item reject-btn"
                                                                        data-id="{{ $item->id }}"
                                                                        data-url="{{ route('itemBasicPrice.reject', $item->id) }}">
                                                                    <i class="fa-solid fa-ban me-2" style="color: #ff3c2e;"></i> Reject
                                                                </button>
                                                            </li>
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
        </section>

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
                            Are you sure you want to <strong>approve</strong> this basic price request?
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
                            Are you sure you want to <strong>reject</strong> this basic price request?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Approve All Confirmation Modal -->
        <div class="modal fade" id="approveAllModal" tabindex="-1" aria-labelledby="approveAllModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="approveAllForm" method="POST" action="{{ route('itemBasicPrice.approveAll') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="request_ids" id="approveAllRequestIds">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Approve All</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="width:50px"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to <strong>approve all</strong> pending basic price requests?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Yes, Approve All</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reject All Confirmation Modal -->
        <div class="modal fade" id="rejectAllModal" tabindex="-1" aria-labelledby="rejectAllModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="rejectAllForm" method="POST" action="{{ route('itemBasicPrice.rejectAll') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="request_ids" id="rejectAllRequestIds">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Reject All</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="width:50px"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to <strong>reject all</strong> pending basic price requests?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Reject All</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const approveButtons = document.querySelectorAll('.approve-btn');
        const rejectButtons = document.querySelectorAll('.reject-btn');
        const approveAllButton = document.querySelector('.approve-all-btn');
        const rejectAllButton = document.querySelector('.reject-all-btn');

        // Individual Approve
        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('approveForm').action = this.dataset.url;
                const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                modal.show();
            });
        });

        // Individual Reject
        rejectButtons.forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('rejectForm').action = this.dataset.url;
                const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                modal.show();
            });
        });

        // === APPROVE ALL ===
        approveAllButton.addEventListener('click', function (e) {
            const requestIds = Array.from(approveButtons).map(btn => btn.dataset.id);

            if (requestIds.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Requests',
                    text: 'There are no pending requests.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                return;
            }

            document.getElementById('approveAllRequestIds').value = requestIds.join(',');
            const modal = new bootstrap.Modal(document.getElementById('approveAllModal'));
            modal.show();
        });

        // === REJECT ALL ===
        rejectAllButton.addEventListener('click', function (e) {
            const requestIds = Array.from(rejectButtons).map(btn => btn.dataset.id);

            if (requestIds.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Requests',
                    text: 'There are no pending requests.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                return;
            }

            document.getElementById('rejectAllRequestIds').value = requestIds.join(',');
            const modal = new bootstrap.Modal(document.getElementById('rejectAllModal'));
            modal.show();
        });
    });
</script>
@endpush
@endsection
