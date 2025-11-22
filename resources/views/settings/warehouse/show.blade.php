@extends('layouts.main')
@section('title', 'Warehouse Management - View Warehouse')
@section('content')

<main id="main" class="main">
    <div class="dashboard-header pagetitle">
        <h1>View Warehouse Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Settings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('warehouse.index') }}">Warehouse</a></li>
                <li class="breadcrumb-item active">View Warehouse</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-2">
                    <div class="card-body mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Warehouse Name:</label>
                                <p class="form-control-static">{{ $warehouse->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Mobile No:</label>
                                <p class="form-control-static">{{ $warehouse->mobile_no ?? 'N/A'}}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">PAN:</label>
                                <p class="form-control-static">{{ $warehouse->pan_no ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">GST No:</label>
                                <p class="form-control-static">{{ $warehouse->gst_no ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">State:</label>
                                <p class="form-control-static">{{ $warehouse->state->state ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">City:</label>
                                <p class="form-control-static">{{ $warehouse->city->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Pincode:</label>
                                <p class="form-control-static">{{ $warehouse->pincode ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Address:</label>
                                <p class="form-control-static">{{ $warehouse->address }}</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('warehouse.edit', $warehouse->id) }}" class="btn custom-btn-primary me-2">Edit</a>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('warehouse.index') }}'">Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection

@push('styles')
<style>
    .form-control-static {
        padding: 12px 15px;
        font-size: 14px;
        background-color: #f4f6fc;
        border-radius: 6px;
        border: 1px solid #ced4da;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
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

    .breadcrumb-item a {
        color: #4e73df;
        font-weight: 600;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .btn {
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush
