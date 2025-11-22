@extends('layouts.main')
@section('title', 'Item Basic Prices - Singhal')
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

        @if (session('import_errors'))
            @push('scripts')
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Basic Price Alert',
                            html: `<ul style="text-align: left; padding-left: 20px;">${@json(session('import_errors')).map(error => `<li>${error}</li>`).join('')}</ul>`,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545',
                            customClass: {
                                popup: 'swal-wide',
                            },
                        });
                    });
                </script>
            @endpush
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
            <h1>Item Basic Prices</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Item Master</a></li>
                    <li class="breadcrumb-item">Item Basic Prices</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                @can('ItemBasicPrice-Import/Export')
                    <div class="col-lg-12">
                        <div class="card p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <form action="{{ route('itemBasicPrice.import') }}" method="POST"
                                        enctype="multipart/form-data">
                                        <label for="file" class="form-label">File<span class="text-danger"> *</span></label>
                                        @csrf
                                        <input class="form-control" type="file" name="file" accept=".xlsx, .csv" required>
                                        <div class="mt-2">
                                            <p style="color: red">Note:
                                                Steps :
                                                </br>
                                                1. Click the Export Template button and download the template
                                                </br>
                                                2. Select the file and click the import button
                                                </br>
                                                3. Please ensure the <strong>Excel file</strong> data is accurate.
                                            </p>
                                        </div>
                                        @error('file')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-success">Import</button>
                                            <a href="{{ route('itemBasicPrice.export') }}" class="btn btn-primary">Export Template</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                <div class="col-lg-12">
                    <div class="card mt-2">
                        <div class="card-body mt-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="pd-20">
                                        <h4 class="text-dark h4 fw-bold mb-0">
                                            <i class="bi bi-cash-coin me-2 text-primary"></i>Item Basic Prices
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex justify-content-md-end justify-content-start mt-3 mt-md-0">
                                    <div class="btn-toolbar gap-3">

                                        @can('ItemBasicPrice-Create')
                                            <a href="{{ route('itemBasicPrice.rejected') }}" class="btn custom-btn-secondary btn-icon">
                                                <i class="bi bi-eye-fill"></i>
                                                <span>Rejected Requests</span>
                                            </a>
                                        @endcan

                                        <!-- Approval Requests with Notification Badge -->
                                        @can('ItemBasicPrice-Approve')
                                            <div class="btn-with-badge">
                                                <a href="{{ route('itemBasicPrice.approvalRequests') }}"
                                                    class="btn custom-btn-secondary btn-icon">
                                                    <i class="bi bi-eye-fill"></i>
                                                    <span>Approval Requests</span>
                                                </a>
                                                <!-- Dynamic badge count (e.g., from controller) -->
                                                @if ($numberOfRequests !== 0)
                                                    <span class="notif-badge">{{ $numberOfRequests }}</span>
                                                @endif
                                            </div>
                                        @endcan

                                        <!-- Add Basic Price Button -->
                                        @can('ItemBasicPrice-Create')
                                            <a class="btn custom-btn-primary btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#modal1">
                                                <i class="bi bi-plus-circle-fill"></i>
                                                <span>Add Basic Price</span>
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            <table class="display stripe row-border order-column" id="item_basic_price_table"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text__left">#</th>
                                        <th class="text__left">ITEM</th>
                                        <th class="text__left">STATE</th>
                                        <th class="text__left">MARKET BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">DISTRIBUTOR BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">DEALER BASIC PRICE(₹/MT)</th>
                                        <th class="text__left">APPROVAL TIME</th>
                                        <th class="text__left">APPROVED BY</th>
                                        <th class="text__left">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemBasicPrices as $i => $item)
                                        <tr>
                                            <td class="text__left">{{ $i + 1 }}</td>
                                            <td class="text__left">{{ $item->itemName->item_name }}</td>
                                            <td class="text__left">{{ $item->stateName?->state }}</td>
                                            <td class="text__left">{{ $item->market_basic_price }}</td>
                                            <td class="text__left">{{ $item->distributor_basic_price }}</td>
                                            <td class="text__left">{{ $item->dealer_basic_price }}</td>
                                            <td class="text__left">{{ $item->approval_date->format('j F, Y h:i A') }}</td>
                                            <td class="text__left">{{ $item->approved_by }}</td>
                                            <td>
                                                <div class="dropdown-container">
                                                    <div class="dropdown">
                                                        <span class="btn btn-sm btn-primary dropdown-toggle"
                                                              id="actionMenu{{ $item->id }}" data-bs-toggle="dropdown"
                                                              aria-expanded="false">
                                                            <i class="fa-solid fa-list"></i>
                                                        </span>
                                                        <ul class="dropdown-menu custom-dropdown-menu"
                                                            aria-labelledby="actionMenu{{ $item->id }}">
                                                            @can('ItemBasicPrice-View')
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                       href="{{ route('itemBasicPrices.show', $item->id) }}">
                                                                        <i class="fas fa-eye me-2 text-primary"></i> View
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('ItemBasicPrice-Edit')
                                                                @if ($item->status === 'Approved')
                                                                    <li>
                                                                        <a class="dropdown-item edit-btn"
                                                                           data-id="{{ $item->id }}"
                                                                           data-item="{{ $item->item }}"
                                                                           data-item_name="{{ $item->itemName->item_name }}"
                                                                           data-region="{{ $item->region }}"
                                                                           data-state="{{ $item->stateName->state }}"
                                                                           data-market_basic_price="{{ $item->market_basic_price }}"
                                                                           data-distributor_basic_price="{{ $item->distributor_basic_price }}"
                                                                           data-dealer_basic_price="{{ $item->dealer_basic_price }}"
                                                                           data-remarks="{{ htmlspecialchars($item->remarks ?? '') }}"
                                                                           data-bs-toggle="modal" data-bs-target="#modal2">
                                                                            <i class="fas fa-edit me-2 text-warning"></i> Edit
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endcan
                                                        </ul>
                                                    </div>
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
                    <h5 class="modal-title" id="modal1Label">Add New Basic Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width:50px"></button>
                </div>
                <form action="{{ route('itemBasicPrices.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <label for="item" class="col-sm-12 col-form-label"><strong>Item</strong></label>
                            <div class="col-sm-12">
                                <input value=1 type="integer" class="form-control" name="item" id="item"
                                       required hidden>
                                <input value="SINGHAL TMT" type="string" class="form-control" name="item_name"
                                       id="item_name" required readonly>
                            </div>

                            <label for="region" class="col-sm-12 col-form-label"><strong>State</strong><span
                                    class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <select class="form-select select2" aria-label="Default select example" name="region"
                                        id="region" required>
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label for="market_basic_price" class="col-sm-12 col-form-label"><strong>Market Basic
                                    Price(₹/MT)</strong><span class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <input step="0.01" oninput="validateDecimal(this)" type="number"
                                       class="form-control" name="market_basic_price" id="market_basic_price" required>
                            </div>

                            <label for="distributor_basic_price" class="col-sm-12 col-form-label"><strong>Distributor
                                    Basic Price(₹/MT)</strong><span class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <input step="0.01" oninput="validateDecimal(this)" type="number"
                                       class="form-control" name="distributor_basic_price" id="distributor_basic_price"
                                       required>
                            </div>

                            <label for="dealer_basic_price" class="col-sm-12 col-form-label"><strong>Dealer Basic
                                    Price(₹/MT)</strong><span class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <input step="0.01" oninput="validateDecimal(this)" type="number"
                                       class="form-control" name="dealer_basic_price" id="dealer_basic_price" required>
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

    {{-- Modal to edit existing item basic price --}}
    <div class="modal fade" id="modal2" tabindex="-1" aria-labelledby="modal2Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal2Label">Update Item Basic Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width:50px"></button>
                </div>
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id"> <!-- Hidden ID -->
                    <div class="modal-body">
                        <div class="row">
                            <label for="edit_item" class="col-sm-12 col-form-label"><strong>Item</strong></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="item" id="edit_item" required
                                       hidden>
                                <input type="text" class="form-control" name="item_name" id="edit_item_name" required
                                       readonly>
                            </div>

                            <label for="edit_region" class="col-sm-12 col-form-label"><strong>State</strong></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="region" id="edit_region" required
                                       hidden>
                                <input type="text" class="form-control" name="state" id="edit_state" required
                                       readonly>
                            </div>

                            <label for="edit_market_basic_price" class="col-sm-12 col-form-label"><strong>Market Basic
                                    Price(₹/MT)</strong><span class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <input oninput="validateDecimal(this)" step="0.01" type="number"
                                       class="form-control" name="market_basic_price" id="edit_market_basic_price" required>
                            </div>

                            <label for="edit_distributor_basic_price" class="col-sm-12 col-form-label"><strong>Distributor
                                    Basic Price(₹/MT)</strong><span class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <input oninput="validateDecimal(this)" step="0.01" type="number"
                                       class="form-control" name="distributor_basic_price" id="edit_distributor_basic_price"
                                       required>
                            </div>

                            <label for="edit_dealer_basic_price" class="col-sm-12 col-form-label"><strong>Dealer Basic
                                    Price(₹/MT)</strong><span class="required-classes">*</span></label>
                            <div class="col-sm-12">
                                <input oninput="validateDecimal(this)" step="0.01" type="number"
                                       class="form-control" name="dealer_basic_price" id="edit_dealer_basic_price" required>
                            </div>

                            <label for="edit_remarks" class="col-sm-12 col-form-label"><strong>Remarks</strong></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="remarks" id="edit_remarks">
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
                        Are you sure you want to mark this item as <strong>inactive</strong>?
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

                    const modal = new bootstrap.Modal(document.getElementById('confirmInactiveModal'));
                    modal.show();
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButtons = document.querySelectorAll('.edit-btn');

            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    console.log('Remarks:', this.dataset.remarks); // Debug remarks value
                    const id = this.dataset.id;
                    const item = this.dataset.item;
                    const item_name = this.dataset.item_name;
                    const region = this.dataset.region;
                    const state = this.dataset.state;
                    const marketPrice = this.dataset.market_basic_price;
                    const distributorPrice = this.dataset.distributor_basic_price;
                    const dealerPrice = this.dataset.dealer_basic_price;
                    const remarks = this.dataset.remarks || ''; // Fallback to empty string if undefined

                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_item').value = item;
                    document.getElementById('edit_item_name').value = item_name;
                    document.getElementById('edit_region').value = region;
                    document.getElementById('edit_state').value = state;
                    document.getElementById('edit_market_basic_price').value = marketPrice;
                    document.getElementById('edit_distributor_basic_price').value = distributorPrice;
                    document.getElementById('edit_dealer_basic_price').value = dealerPrice;
                    document.getElementById('edit_remarks').value = remarks;

                    // Set form action
                    const form = document.getElementById('editForm');
                    form.action = '/item-basic-prices/update/' + id;
                });
            });
        });
    </script>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize Select2 for the state dropdown in the Add New Basic Price modal
                $('#region').select2({
                    placeholder: "Select State",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal1') // Ensures dropdown is attached to modal for proper z-index
                });

                // Dropdown positioning for Datatables
                $('#item_basic_price_table').on('show.bs.dropdown', function(e) {
                    const dropdownMenu = $(e.target).find('.dropdown-menu');
                    if (dropdownMenu.length > 0) {
                        const dropdownParent = $(e.target).closest('td');
                        const dropdownOffset = dropdownParent.offset();
                        const dropdownHeight = dropdownMenu.outerHeight();
                        const windowHeight = $(window).height();
                        const tableScrollTop = $(window).scrollTop();

                        // Check if there is enough space below the dropdown
                        if ((dropdownOffset.top + dropdownHeight) > (windowHeight + tableScrollTop)) {
                            dropdownMenu.addClass('dropdown-menu-end');
                            dropdownMenu.css({
                                top: 'auto',
                                bottom: '100%',
                                right: '0',
                                left: 'auto',
                                'margin-bottom': '5px'
                            });
                        } else {
                            dropdownMenu.removeClass('dropdown-menu-end');
                            dropdownMenu.css({
                                top: '100%',
                                bottom: 'auto',
                                right: 'auto',
                                left: '50%',
                                transform: 'translateX(-50%)',
                                'margin-top': '5px'
                            });
                        }
                    }
                });
            });
        </script>
    @endpush

    <script>
        function validateDecimal(input) {
            const value = input.value;
            if (!/^(\d+(\.\d{0,2})?)?$/.test(value)) {
                input.value = value.slice(0, -1); // remove last character
            }
        }
    </script>

    @push('styles')
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            #item_basic_price_table {
                border-collapse: separate;
                border-spacing: 0 10px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 15px;
            }

            /* Table header styling with a subtle gradient and rounded corners */
            #item_basic_price_table thead tr {
                background: linear-gradient(to right, #6a85b6, #bac8e0);
                color: #fff;
                border-radius: 8px 8px 0 0;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                font-size: 16px;
            }

            #item_basic_price_table th {
                padding: 15px 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Table body row styling with hover effect */
            #item_basic_price_table tbody tr {
                background-color: #f7f9fc;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            #item_basic_price_table tbody tr:hover {
                background-color: #e9ecef;
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
                z-index: 1;
                position: relative;
            }

            /* Table cell styling */
            #item_basic_price_table td {
                padding: 12px 10px;
                color: #495057;
                border: none;
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
            }

            .dropdown-menu.custom-dropdown-menu .dropdown-item {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 1rem;
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

            /* Select2 styling */
            .select2-container--default .select2-selection--single {
                border: 1px solid #ced4da;
                border-radius: 4px;
                height: 38px;
                display: flex;
                align-items: center;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 38px;
                padding-left: 10px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 38px;
                right: 10px;
            }

            .select2-container--default .select2-selection--single .select2-selection__placeholder {
                color: #6c757d;
            }

            .select2-container .select2-dropdown {
                border: 1px solid #ced4da;
                border-radius: 4px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
        </style>

        {{-- CSS Styling for buttons --}}
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
        </style>
        <style>
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

    @push('scripts')
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @endpush
@endsection
