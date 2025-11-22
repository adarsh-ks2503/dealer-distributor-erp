@extends('layouts.main')
@section('title', 'Warehouse Management - Singhal')
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
                    <i class="fas fa-solid fa-check check"></i>
                    <div class="message">
                        <span class="text text-1">Error</span>
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
                        <span class="text text-2">{{ $errors }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        <div class="dashboard-header pagetitle">
            <h1>Warehouse Management</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Warehouse Management</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-2">
                        <div class="card-body mt-4">
                            @can('Warehouse-Create')
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <a href="{{ route('warehouse.create') }}" type="button"
                                        class="btn custom-btn-primary ms-auto">
                                        Add New Warehouse
                                    </a>
                                </div>
                            @endcan

                            <div class="table-responsive">
                                <table class="table warehouse-table" id="warehouses_table">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Warehouse Name</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                            <th>Pincode</th>
                                            <th>GST NO.</th>
                                            <th>PAN</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($warehouses as $index => $warehouse)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $warehouse->name }}</td>
                                                <td>{{ $warehouse->mobile_no ?? 'N/A' }}</td>
                                                <td>{{ $warehouse->address }}, {{ $warehouse->city->name ?? '' }},
                                                    {{ $warehouse->state->state ?? '' }}</td>
                                                <td>{{ $warehouse->pincode ?? 'N/A' }}</td>
                                                <td>{{ $warehouse->gst_no ?? 'N/A' }}</td>
                                                <td>{{ $warehouse->pan_no ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn action-btn dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu animated-dropdown">
                                                            @can('Warehouse-View')
                                                                <li><a class="dropdown-item" href="{{ route('warehouse.show',$warehouse->id) }}"><i
                                                                            class="fa fa-eye me-2 text-primary"></i>View</a>
                                                                </li>
                                                            @endcan
                                                            @can('Warehouse-Edit')
                                                                <li><a class="dropdown-item" href="{{ route('warehouse.edit', $warehouse->id) }}"><i
                                                                            class="fa fa-edit me-2 text-warning"></i>Edit</a>
                                                                </li>
                                                            @endcan
                                                            {{-- @can('Warehouse-Delete') --}}
                                                                {{-- <li>
                                                                    <form action="#" method="POST"
                                                                        onsubmit="return confirm('Are you sure you want to delete this warehouse?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="dropdown-item text-danger"
                                                                            type="submit">
                                                                            <i class="fa fa-trash me-2 text-danger"></i>Delete
                                                                        </button>
                                                                    </form>
                                                                </li> --}}
                                                            {{-- @endcan --}}
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No warehouse records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @push('styles')
        <style>
            .warehouse-table {
                border-collapse: collapse;
                /* Ensures borders collapse into a single line */
                width: 100%;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                /* Add subtle shadow for depth */
            }

            .warehouse-table thead tr {
                background: #4e73df;
                /* Soft blue for header */
                color: white;
                text-align: left;
            }

            .warehouse-table thead th {
                padding: 14px 10px;
                text-transform: uppercase;
                font-size: 14px;
                border: 1px solid #ddd;
                /* Subtle border around header */
                border-bottom: 3px solid #3e5bb0;
                /* Thicker bottom border for emphasis */
            }

            .warehouse-table tbody tr {
                background: #ffffff;
                transition: all 0.3s ease;
                /* Smooth transition for hover effect */
            }

            /* .warehouse-table tbody tr:hover {
                                        background: #f1f5f9;
                                        transform: scale(1.01);
                                    } */

            .warehouse-table td {
                padding: 12px 15px;
                vertical-align: middle;
                font-size: 15px;
                border: 1px solid #e0e0e0;
                /* Lighter borders for table cells */
                text-align: left;
            }

            .warehouse-table td:first-child,
            .warehouse-table th:first-child {
                border-left: 2px solid #4e73df;
                /* Left border for the first column */
            }

            .warehouse-table td:last-child,
            .warehouse-table th:last-child {
                border-right: 2px solid #4e73df;
                /* Right border for the last column */
            }

            .action-btn {
                background: #5c6bc0;
                /* Soft blue for the action button */
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
                background: #3e5bb0;
                /* Darker blue on hover */
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
                background: linear-gradient(135deg, #5c6bc0, #3e5bb0);
                /* Soft blue gradient for button */
                color: #fff;
                border: none;
                padding: 10px 24px;
                border-radius: 8px;
                font-weight: 500;
                transition: all 0.3s ease-in-out;
            }

            .custom-btn-primary:hover {
                background: linear-gradient(135deg, #3e5bb0, #607d8b);
                /* Slightly darker gradient on hover */
                transform: translateY(-2px);
                color: #fff;
            }
        </style>
    @endpush


    {{-- ================= Warehouse Modal ================= --}}
    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Add New Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="warehouseForm">
                    <div class="modal-body">
                        <!-- Add form fields for warehouse details here -->
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn custom-btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
