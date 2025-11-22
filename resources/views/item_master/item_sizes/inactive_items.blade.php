@extends('layouts.main')
@section('title', 'Item Sizes - Singhal')
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

        @if ($errors->any())
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-xmark-circle error"></i>
                    <div class="message">
                        <span class="text text-1">Error</span>
                        <span class="text text-2">Company Setting Update UnSuccessful</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif
        <div class="dashboard-header pagetitle">
            <h1>Item Size</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Item Sizes</li>
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
                                        <h4 class="text-blue h4">Inactive Item Sizes</h4>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex justify-content-end ">
                                    <div class="btn-group">
                                        <div>
                                            <a class="btn btn-primary mb-4 mr-3" href="{{ route('itemSizes.index') }}">
                                                Back
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="display stripe row-border order-column" id="item_size_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text__left">#</th>
                                        <th>ITEM</th>
                                        <th class="text__left">SIZE(mm)</th>
                                        <th class="text__left">RATE</th>
                                        <th>HSN CODE</th>
                                        <th>REMARK</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemSizes as $i=>$size)
                                        <tr>
                                            <td class="text__left">{{ $i+1 }}</td>
                                            <td>{{ $size->item }}</td>
                                            <td class="text__left">{{ $size->size }}</td>
                                            <td class="text__left">{{ $size->rate }}</td>
                                            <td>{{ $size->hsn_code }}</td>
                                            <td>{{ $size->remarks }}</td>
                                            <td>{{ $size->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <span class="btn btn-sm btn-primary dropdown-toggle"
                                                        id="actionMenu{{ $size->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-solid fa-list"></i>
                                                    </span>
                                                    <ul class="dropdown-menu custom-dropdown-menu"
                                                        aria-labelledby="actionMenu{{ $size->id }}">
                                                        @can('ItemSize-View')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('itemSizes.show', $size->id) }}">
                                                                    <i class="fas fa-eye me-2 text-primary"></i> View
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('ItemSize-Active')
                                                            <li>
                                                                <button class="dropdown-item text-success open-inactive-modal"
                                                                    data-id="{{ $size->id }}"
                                                                    data-action="{{ route('itemSizes.activate', $size->id) }}">
                                                                    <i class="fas fa-ban me-2 text-success"></i> Mark Active
                                                                </button>
                                                            </li>
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

    </main><!-- End #main -->

    <!-- Custom Confirm Active Modal -->
    <div class="modal fade" id="confirmInactiveModal" tabindex="-1" aria-labelledby="confirmInactiveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="inactiveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmInactiveModalLabel">Confirm Activation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width:50px"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to mark this item as <strong>Active</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Yes, Mark Active</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inactiveButtons = document.querySelectorAll('.open-inactive-modal');
            const form = document.getElementById('inactiveForm');

            inactiveButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.dataset.action;
                    form.action = action;

                    const modal = new bootstrap.Modal(document.getElementById(
                        'confirmInactiveModal'));
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButtons = document.querySelectorAll('.edit-btn');

            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const item = this.dataset.item;
                    const hsn = this.dataset.hsn;
                    const size = this.dataset.size;
                    const rate = this.dataset.rate;
                    const remarks = this.dataset.remarks;

                    document.getElementById('edit_id').value = id;
                    document.getElementById('item_edit').value = item;
                    document.getElementById('hsn_code_edit').value = hsn;
                    document.getElementById('size_edit').value = size;
                    document.getElementById('rate_edit').value = rate;
                    document.getElementById('remarks_edit').value = remarks;

                    // Update the form action
                    const form = document.getElementById('editForm');
                    form.action = '/item-sizes/update/' + id;
                });
            });
        });
    </script>

@endsection
