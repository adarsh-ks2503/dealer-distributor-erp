@extends('layouts.main')
@section('title', 'Item Bundles - Singhal')
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
                        <span class="text text-2">This Item Size already exists. Cannot have duplicate sizes!</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif
        <div class="dashboard-header pagetitle">
            <h1>Item Bundles</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Item Master</a></li>
                    <li class="breadcrumb-item">Item Bundles</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-2">
                        <div class="card-body mt-4">

                            <div class="row align-items-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="pd-20">
                                        <h4 class="text-dark h4 fw-bold mb-0">
                                            <i class="bi bi-aspect-ratio me-2 text-primary"></i>Item Bundles
                                        </h4>
                                    </div>
                                </div>

                                <div
                                    class="col-md-6 col-sm-12 d-flex justify-content-md-end justify-content-start mt-3 mt-md-0">
                                    <div class="btn-toolbar gap-3">

                                        <!-- Show Inactive Item Sizes -->

                                        @can('ItemBundle-Active')
                                            <a href="{{ route('itemSizes.inactiveSizes') }}"
                                                class="btn custom-btn-secondary btn-icon">
                                                <i class="bi bi-eye-slash-fill"></i>
                                                <span>Inactive Bundles</span>
                                            </a>
                                        @endcan

                                        <!-- Add New Size Button -->
                                        @can('ItemBundle-Create')
                                            <a class="btn custom-btn-primary btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#modal1">
                                                <i class="bi bi-plus-circle-fill"></i>
                                                <span>Add New Bundle</span>
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            <table class="display stripe row-border order-column" id="item_bundle_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text__left">Bundle Name</th>
                                        <th class="text__left">Size(mm)</th>
                                        <th class="text__left">Number of Pieces</th>
                                        <th>Weight(Kg)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bundles as $bundle)
                                        <tr>
                                            <td>{{ $bundle->itemName?->item_name }}</td>
                                            <td class="text__left">{{ $bundle->bundle_name }}</td>
                                            <td class="text__left">{{ $bundle->sizeDetail->size }}</td>
                                            <td class="text__left">{{ $bundle->pieces }}</td>
                                            <td>{{ $bundle->initial_range }} - {{ $bundle->end_range }}</td>
                                            <td>{{ $bundle->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <span class="btn btn-sm btn-primary dropdown-toggle"
                                                        id="actionMenu{{ $bundle->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-solid fa-list"></i>
                                                    </span>
                                                    <ul class="dropdown-menu custom-dropdown-menu"
                                                        aria-labelledby="actionMenu{{ $bundle->id }}">
                                                        @can('ItemBundle-View')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('itemSizes.show', $bundle->id) }}">
                                                                    <i class="fas fa-eye me-2 text-primary"></i> View
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('ItemBundle-Edit')
                                                            @if ($bundle->status === 'Active')
                                                                <li>
                                                                    <a class="dropdown-item edit-btn">
                                                                        {{-- data-id="{{ $size->id }}"
                                                                data-item="{{ $size->item }}"
                                                                data-item-name="{{ $size->itemName?->item_name }}"
                                                                data-hsn="{{ $size->hsn_code }}"
                                                                data-size="{{ $size->size }}"
                                                                data-rate="{{ $size->rate }}"
                                                                data-remarks="{{ $size->remarks }}"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modal2"> --}}
                                                                        <i class="fas fa-edit me-2 text-warning"></i> Edit
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        @endif
                                                        @can('ItemBundle-Inactive')
                                                            <li>
                                                                <button class="dropdown-item text-danger open-inactive-modal"
                                                                    data-id="{{ $bundle->id }}"
                                                                    data-action="{{ route('itemSizes.inactive', $bundle->id) }}">
                                                                    <i class="fas fa-ban me-2 text-danger"></i> Mark Inactive
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

    {{-- Modal to add new item size --}}
    <div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal1Label">Add Item Bundle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="width:50px"></button>
                </div>
                <form action="{{ route('itemBundle.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row  ">

                            <label for="item" class="col-sm-12 col-form-label"><strong>Item</strong></label>
                            <div class="col-sm-12">
                                <input value="TMT Bar" type="text" class="form-control" name="item_name" id="item"
                                    required readonly>
                                <input value=1 type="numeric" class="form-control" name="item_id" id="item" required
                                    hidden>
                            </div>

                            <label for="hsn_code" class="col-sm-12 col-form-label"><strong>Bundle Name</strong><span
                                    class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="bundle_name" id="hsn_code" required>
                            </div>

                            <label for="size" class="col-sm-12 col-form-label"><strong>Size(mm)</strong><span
                                    class="required-classes">*</span> </label>
                            <div class="col-sm-12">
                                <select class="form-select" id="size" name="size_id"
                                    aria-label="Default select example">
                                    <option selected>Open this select menu</option>
                                    @foreach ($sizes as $size)
                                        <option value={{ $size->id }}>{{ $size->size }} mm</option>
                                    @endforeach
                                </select>
                            </div>

                            <label for="rate" class="col-sm-12 col-form-label"><strong>Number Of Pieces</strong><span
                                    class="required-classes">*</span> </label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="pieces" id="rate" required>
                            </div>

                            <div class="row">

                                <div class="col-sm-6">
                                    <label for="rate" class="col-sm-12 col-form-label"><strong>Initial
                                            Range(Kg)</strong><span class="required-classes">*</span> </label>
                                    <div class="col-sm-12">
                                        <input type="number" step="0.001" class="form-control" name="initial_range"
                                            id="rate" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label for="rate" class="col-sm-12 col-form-label"><strong>End
                                            Range(Kg)</strong><span class="required-classes">*</span> </label>
                                    <div class="col-sm-12">
                                        <input type="number" step="0.001" class="form-control" name="end_range"
                                            id="rate" required>
                                    </div>
                                </div>

                            </div>

                            <label for="remarks" class="col-sm-12 col-form-label"><strong>Remarks</strong></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="remarks" id="remarks">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal to edit existing item size --}}
    <div class="modal fade" id="modal2" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal1Label">Update Item Size</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="width:50px"></button>
                </div>
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id"> <!-- Hidden ID -->
                    <div class="modal-body">
                        <div class="row  ">
                            <label for="item" class="col-sm-12 col-form-label"><strong>Item</strong></label>
                            <div class="col-sm-12">
                                <input value="TMT Bar" type="text" class="form-control" name="item_name"
                                    id="item_name_edit" required readonly>

                                <input type="text" class="form-control" name="item" id="item_edit" required
                                    hidden>
                            </div>

                            <label for="hsn_code" class="col-sm-12 col-form-label"><strong>HSN Code</strong></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="hsn_code" id="hsn_code_edit">
                            </div>

                            <label for="size" class="col-sm-12 col-form-label"><strong>Size(mm)</strong><span
                                    class="required-classes">*</span> </label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="size" id="size_edit" readonly>
                            </div>

                            <label for="rate" class="col-sm-12 col-form-label"><strong>Rate</strong><span
                                    class="required-classes">*</span> </label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="rate" id="rate_edit" required>
                            </div>

                            <label for="remarks" class="col-sm-12 col-form-label"><strong>Remark</strong></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="remarks" id="remarks_edit">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Confirm Inactive Modal -->
    <div class="modal fade" id="confirmInactiveModal" tabindex="-1" aria-labelledby="confirmInactiveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="inactiveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmInactiveModalLabel">Confirm Inactivation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width:50px"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to mark this bundle as <strong>inactive</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Mark Inactive</button>
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
            div.dataTables_scrollBody {
                overflow: visible !important;
                position: relative !important;
                z-index: 100;
            }

            table.dataTable td {
                overflow: visible !important;
                position: relative !important;
                z-index: 1;
            }

            .dropdown-menu.custom-dropdown-menu {
                z-index: 9999 !important;
                position: absolute !important;
            }

            /* Base table styling for a clean look */
            #item_size_table {
                border-collapse: separate;
                border-spacing: 0 10px;
                /* Thodi si space rows ke beech mein */
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 15px;
                /* Font size badhaya */
            }

            /* Table header styling with a subtle gradient and rounded corners */
            #item_size_table thead tr {
                background: linear-gradient(to right, #6a85b6, #bac8e0);
                color: #fff;
                border-radius: 8px 8px 0 0;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                font-size: 16px;
                /* Header font size */
            }

            #item_size_table th {
                padding: 15px 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Table body row styling with hover effect */
            #item_size_table tbody tr {
                background-color: #f7f9fc;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            #item_size_table tbody tr:hover {
                background-color: #e9ecef;
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
                z-index: 1;
                /* Optional but safe */
                position: relative;
            }

            /* Table cell styling */
            #item_size_table td {
                padding: 12px 10px;
                color: #495057;
                border: none;
                /* Borders hataye taaki clean look mile */
                vertical-align: middle;
            }

            /* Dropdown button styling */
            .dropdown .btn.dropdown-toggle {
                background-color: #5d87ff;
                border: none;
                border-radius: 50px;
                width: 38px;
                height: 38px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .dropdown .btn.dropdown-toggle:hover {
                transform: scale(1.1);
                /* Hover par thoda bada hoga */
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            }

            /* Dropdown menu styling */
            .dropdown-menu.custom-dropdown-menu {
                animation: fadeInScale 0.3s ease-in-out;
                border-radius: 12px;
                padding: 8px 0;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
                min-width: 180px;
                border: 1px solid #e9ecef;
                z-index: 1050;
                /* Z-index badhaya */
            }

            .dropdown-menu.custom-dropdown-menu .dropdown-item {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 1rem;
                /* Font size badhaya */
                color: #495057;
                padding: 12px 20px;
                transition: background-color 0.2s ease, transform 0.2s ease;
            }

            .dropdown-menu.custom-dropdown-menu .dropdown-item:hover {
                background-color: #f1f3f5;
                transform: translateX(5px);
                color: #212529;
            }

            /* Animation for dropdown */
            @keyframes fadeInScale {
                0% {
                    opacity: 0;
                    transform: scale(0.9) translateY(-10px);
                }

                100% {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }

            /* Override Datatables default styling */
            table.dataTable td {
                overflow: visible !important;
                position: static !important;
            }

            /* Dropdown positioned outside the cell */
            .dropdown-container {
                position: relative;
                z-index: 1000;
            }

            .dropdown-container .dropdown-menu {
                position: absolute;
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                margin-top: 5px;
            }
        </style>
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
    @endpush

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButtons = document.querySelectorAll('.edit-btn');

            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const item = this.dataset.item;
                    const item_name = this.dataset.item - name;
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
                    form.action = '/item-bundle/update/' + id;
                });
            });
        });
    </script>

@endsection
