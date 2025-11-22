@extends('layouts.main')
@section('title', 'View Dealer')

@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <div class="card dealer-header-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="card-title">Dealer</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dealers.index') }}">Dealers</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('dealers.index') }}" class="btn btn-back">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card detail-card p-4 mb-4">
            <h5 class="fw-bold text-uppercase mb-3">Dealer Basic Details</h5>
            <table class="table table-custom">
                <tbody>
                    <tr>
                        <th>Dealer Code</th>
                        <td>{{ $dealer->code }}</td>
                        <th>Dealer Name</th>
                        <td>{{ $dealer->name }}</td>
                    </tr>
                    <tr>
                        <th>Mobile No.</th>
                        <td>{{ $dealer->mobile_no }}</td>
                        <th>Email</th>
                        <td><a href="mailto:{{ $dealer->email }}" class="text-primary">{{ $dealer->email }}</a></td>
                    </tr>
                    <tr>
                        <th>GST No.</th>
                        <td>{{ $dealer->gst_num ?? 'N/A' }}</td>
                        <th>PAN No.</th>
                        <td>{{ $dealer->pan_num ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Order Limit (MT)</th>
                        <td>{{ $dealer->order_limit ?? 'N/A' }} MT</td>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $dealer->status === 'Active' ? 'success' : ($dealer->status === 'Pending' ? 'warning' : ($dealer->status === 'Rejected' ? 'danger' : ($dealer->status === 'Inactive' ? 'danger' : 'secondary'))) }}">{{ $dealer->status }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>{{ ucfirst($dealer->type) }}</td>
                        <th>Assigned Distributor</th>
                        <td>{{ $dealer->distributor->code ?? 'N/A' }} - {{ $dealer->distributor->name ?? '' }}</td>
                    </tr>

                    <tr>
                        <th>Created By</th>
                        <td>{{ ucfirst($dealer->created_by ?? "") }}</td>
                        {{-- <th>Assigned Distributor</th> --}}
                        {{-- <td>{{ $dealer->distributor->code ?? 'N/A' }} - {{ $dealer->distributor->name ?? '' }}</td> --}}
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td class="remark-cell" colspan="3">
                            <textarea disabled name="" id="" cols="30" rows="2" class="form-control bg-light" disabled>{{ $dealer->remarks ?? '-' }}</textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card detail-card p-4 mb-4">
            <h5 class="fw-bold text-uppercase mb-3">Contact Person Details</h5>
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile No.</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dealer->contactPersons as $contact)
                        <tr>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->mobile_no }}</td>
                            <td><a href="mailto:{{ $contact->email }}" class="text-primary">{{ $contact->email }}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No Contact Persons Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card detail-card p-4 mb-4">
            <h5 class="fw-bold text-uppercase mb-3">Dealer Address Details</h5>
            <table class="table table-custom">
                <tbody>
                    <tr>
                        <th>Address</th>
                        <td>{{ $dealer->address ?? 'N/A' }}</td>
                        <th>State</th>
                        <td>{{ $dealer->state->state ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Pincode</th>
                        <td>{{ $dealer->pincode ?? 'N/A' }}</td>
                        <th>City</th>
                        <td>{{ $dealer->city->name ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card detail-card p-4 mb-4">
            <h5 class="fw-bold text-uppercase mb-3">Bank Account Details</h5>
            <table class="table table-custom">
                <tbody>
                    <tr>
                        <th>Bank Name</th>
                        <td>{{ $dealer->bank_name ?? '—' }}</td>
                        <th>Account Holder</th>
                        <td>{{ $dealer->account_holder_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>IFSC Code</th>
                        <td>{{ $dealer->ifsc_code ?? '—' }}</td>
                        <th>Account Number</th>
                        <td>{{ $dealer->account_number ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </section>
</main>

@push('styles')
<style>
    .table-custom th {
        background-color: #f8f9fa;
        font-weight: 600;
        width: 25%;
        padding: 12px;
        border-color: #e9ecef;
    }

    .table-custom td {
        padding: 12px;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .detail-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s ease;
    }

    .detail-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .detail-card h5 {
        font-size: 18px;
        color: #1a2e44;
    }

    .badge {
        padding: 6px 12px;
        font-size: 13px;
        text-transform: uppercase;
    }

    .table-custom td.remark-cell {
        max-width: 400px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table-custom td .form-control {
        border: none;
        background: #f8f9fa;
        resize: none;
    }

    .table-custom td .text-primary {
        color: #4f46e5;
    }

    .table-custom td .text-muted {
        color: #6b7280;
    }

    .dealer-header-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 12px 20px;
        margin-bottom: 20px;
    }

    .dealer-header-card .card-body {
        padding: 0;
    }

    .dealer-header-card .card-title {
        font-size: 24px;
        font-weight: 600;
        color: #1a2e44;
        margin: 0;
    }

    .dealer-header-card .breadcrumb {
        background: transparent;
        font-size: 14px;
        margin-top: 4px;
    }

    .dealer-header-card .breadcrumb-item a {
        color: #1a2e44;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .dealer-header-card .breadcrumb-item a:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    .dealer-header-card .breadcrumb-item.active {
        color: #6b7280;
    }

    .btn-back {
        background: #ffffff;
        color: #1a2e44;
        border: 1px solid #ced4da;
        padding: 6px 16px;
        border-radius: 4px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: #e9ecef;
        color: #1a2e44;
        border-color: #adb5bd;
        transform: translateY(-1px);
    }

    .btn-back i {
        font-size: 12px;
    }
</style>
@endpush
@endsection
