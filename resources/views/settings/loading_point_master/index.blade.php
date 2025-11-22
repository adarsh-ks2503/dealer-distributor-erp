@extends('layouts.main')

@section('title', 'Loading Point Master - Singhal')

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

        <div class="dashboard-header pagetitle">
            <h1>Loading Point Master</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Loading Point Master</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-2">
                        <div class="card-body mt-4">
                            {{-- @can('LoadingPoint-Create') --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <button type="button" class="btn custom-btn-primary ms-auto" data-bs-toggle="modal"
                                        data-bs-target="#warehouseModal">
                                        Add New Loading Point
                                    </button>
                                </div>
                            {{-- @endcan --}}

                            <div class="table-responsive">
                                <table class="table loading-point-table" id="loading_points_table">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Loading Point Name</th>
                                            <th>Warehouse Name</th>
                                            <th>Short Code</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Supervisor Name</th>
                                            <th>Mobile No.</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loadingPoints as $index => $point)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $point->name }}</td>
                                                <td>{{ $point->warehouse->name }}</td> <!-- Access warehouse name -->
                                                <td>{{ $point->short_code }}</td>
                                                <td>{{ $point->warehouse->city->name ?? 'N/A' }}</td>
                                                <!-- Access warehouse city -->
                                                <td>{{ $point->warehouse->state->state ?? 'N/A' }}</td>
                                                <!-- Access warehouse state -->
                                                <td>{{ $point->supervisor_name }}</td>
                                                <td>{{ $point->supervisor_mobile_no }}</td>
                                                <td>{{ $point->status }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn action-btn dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu animated-dropdown">
                                                            {{-- @can('LoadingPoint-View') --}}
                                                                <li><a class="dropdown-item" href="#"><i
                                                                            class="fa fa-eye me-2 text-primary"></i>View</a>
                                                                </li>
                                                            {{-- @endcan --}}
                                                            {{-- @can('LoadingPoint-Edit') --}}
                                                                <li><a class="dropdown-item" href="#"><i
                                                                            class="fa fa-edit me-2 text-warning"></i>Edit</a>
                                                                </li>
                                                            {{-- @endcan --}}
                                                            {{-- @can('LoadingPoint-Delete') --}}
                                                                <li>
                                                                    <form action="#" method="POST"
                                                                        onsubmit="return confirm('Are you sure you want to delete this loading point?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="dropdown-item text-danger"
                                                                            type="submit">
                                                                            <i class="fa fa-trash me-2 text-danger"></i>Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            {{-- @endcan --}}
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
    </main>

    {{-- Modal for selecting warehouse --}}
    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-labelledby="warehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="warehouseModalLabel">Select Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('loadingPointMaster.create') }}" method="GET">
                        <div class="form-group">
                            <label for="warehouse_id">Select Warehouse</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                <option value="">Select a Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn custom-btn-primary ms-3">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .loading-point-table {
            border-collapse: collapse;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .loading-point-table thead tr {
            background: #4e73df;
            color: white;
            text-align: left;
        }

        .loading-point-table thead th {
            padding: 14px 10px;
            text-transform: uppercase;
            font-size: 14px;
            border: 1px solid #ddd;
        }

        .loading-point-table tbody tr {
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .loading-point-table td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 15px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }

        .action-btn {
            background: #5c6bc0;
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
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease-in-out;
        }

        .custom-btn-primary:hover {
            background: linear-gradient(135deg, #3e5bb0, #607d8b);
            transform: translateY(-2px);
            color: #fff;
        }
    </style>
@endpush
