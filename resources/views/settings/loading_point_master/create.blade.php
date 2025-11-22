@extends('layouts.main')
@section('title', 'Create Loading Point - Stylish UI')
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
        <h1>Create Loading Point</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('loadingPointMaster.index') }}">Loading Point Master</a></li>
                <li class="breadcrumb-item active">Create Loading Point</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-4 shadow-lg p-3 rounded">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold">Warehouse Details</h4>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_name" class="form-label">Warehouse Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                                        <input type="text" id="warehouse_name" name="warehouse_name" class="form-control" value="{{ $warehouse->name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_state" class="form-label">State</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" id="warehouse_state" name="warehouse_state" class="form-control" value="{{ $warehouse->state->state ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_city" class="form-label">City</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                                        <input type="text" id="warehouse_city" name="warehouse_city" class="form-control" value="{{ $warehouse->city->name ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_address" class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                        <input type="text" id="warehouse_address" name="warehouse_address" class="form-control" value="{{ $warehouse->address }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="warehouse_pincode" class="form-label">Pincode</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                        <input type="text" id="warehouse_pincode" name="warehouse_pincode" class="form-control" value="{{ $warehouse->pincode }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('loadingPointMaster.store') }}" method="post">
                    @csrf
                    <input type="text" name="warehouse_id" id="" hidden value={{ $warehouse->id }}>
                    <div class="card mt-4 shadow-lg p-3 rounded">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="fw-bold">Loading Point Details</h4>
                            </div>

                            <div class="form-group mb-3">
                                <label for="loading_point_name" class="form-label">Loading Point Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-signs"></i></span>
                                    <input type="text" id="loading_point_name" name="loading_point_name" class="form-control" placeholder="Enter Loading Point Name" required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="short_code" class="form-label">Short Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-code"></i></span>
                                    <input type="text" id="short_code" name="short_code" class="form-control" placeholder="Enter Short Code" required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="dock_gate_no" class="form-label">Dock / Gate No. <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                    <input type="text" id="dock_gate_no" name="dock_gate_no" class="form-control" placeholder="Enter Dock / Gate No." required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="supervisor_name" class="form-label">Supervisor Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    <input type="text" id="supervisor_name" name="supervisor_name" class="form-control" placeholder="Enter Supervisor Name" required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="supervisor_mobile_no" class="form-label">Supervisor Mobile No. <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                    <input type="text" id="supervisor_mobile_no" name="supervisor_mobile_no" class="form-control" placeholder="Enter Supervisor Mobile No." required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="remark" class="form-label">Remark</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <input type="text" id="remark" name="remarks" class="form-control" placeholder="Enter Remark">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('loadingPointMaster.index') }}'">Cancel</button>
                                <button type="submit" class="btn custom-btn-primary ms-3">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
    </section>
</main>

@endsection

@push('styles')
<style>
    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border: 2px solid #3e5bb0;
        box-shadow: 0 0 8px rgba(62, 91, 176, 0.5);
    }

    .input-group-text {
        background-color: #5c6bc0;
        color: white;
    }

    .custom-btn-primary {
        background: linear-gradient(135deg, #5c6bc0, #3e5bb0);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease-in-out;
    }

    .custom-btn-primary:hover {
        background: linear-gradient(135deg, #3e5bb0, #607d8b);
        transform: translateY(-3px);
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
        padding: 12px 28px;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .input-group select {
        background: #f4f6fc;
        transition: background 0.3s ease;
    }

    .input-group select:hover {
        background: #e1e5f3;
    }

    .input-group select:focus {
        background: #f4f6fc;
    }

    .card {
        border: none;
        border-radius: 12px;
        transition: transform 0.3s ease-in-out;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-body {
        background-color: #f8f9fc;
    }

    .input-group {
        margin-bottom: 15px;
    }

</style>
@endpush

@push('scripts')
<script>
    // Add custom JS for state and city dependent selections, or any other interactivity you need
</script>
@endpush
