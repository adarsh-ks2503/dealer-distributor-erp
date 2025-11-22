@extends('layouts.main')
@section('title', 'View Distributor')

@section('content')
<main id="main" class="main">

    <div class="dashboard-header pagetitle">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="card-title">Distributor Details</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Distributors</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('distributors.index') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
    </div>

    <section class="section">

        <div class="card p-4">
            <h5 class="fw-bold text-uppercase mb-3">Distributor Details</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Distributor Name</th>
                        <td colspan="3">{{ $distributor->name }}</td>
                    </tr>
                    <tr>
                        <th>Distributor Code</th>
                        <td>{{ $distributor->code }}</td>
                        <th>Mobile No.</th>
                        <td>{{ $distributor->mobile_no }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:{{ $distributor->email }}">{{ $distributor->email }}</a></td>
                        <th>State</th>
                        <td>{{ $distributor->state->state ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>GST No.</th>
                        <td>{{ $distributor->gst_num ?? 'N/A' }}</td>
                        <th>Order Limit</th>
                        <td>{{ $distributor->order_limit }} MT</td>
                    </tr>
                    <tr>
                        <th>Allowed Order Limit</th>
                        <td>{{ $distributor->allowed_order_limit ?? 'N/A' }} MT</td>
                        <th>Individual Allowed Order Limit</th>
                        <td>{{ $distributor->individual_allowed_order_limit }} MT</td>
                    </tr>
                    <tr>
                        <th>PAN No.</th>
                        <td class="remark-cell">{{ $distributor->pan_num ?? '—' }}</td>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $distributor->status == 'Active' ? 'success' : 'secondary' }}">{{ $distributor->status }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ ucfirst($distributor->created_by ?? "") }}</td>
                        {{-- <th>Assigned Distributor</th> --}}
                        {{-- <td>{{ $dealer->distributor->code ?? 'N/A' }} - {{ $dealer->distributor->name ?? '' }}</td> --}}
                    </tr>
                    <tr>
                        <th>Remark</th>
                        <td class="remark-cell" colspan="3">
                            <textarea disabled name="remarks" id="" cols="30" rows="2" class="form-control @error('remarks') is-invalid @enderror" disabled>{{ old('remarks', $distributor->remarks) }}</textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card p-4 mt-4">
            <h5 class="fw-bold text-uppercase mb-3">Contact Person Details</h5>
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Mobile No.</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($distributor->contactPersons as $contact)
                        <tr>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->mobile_no }}</td>
                            <td><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No Contact Persons Added</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card p-4 mt-4">
            <h5 class="fw-bold text-uppercase mb-3">Distributor Address Details</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Address</th>
                        <td colspan="3">{{ $distributor->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Pincode</th>
                        <td>{{ $distributor->pincode ?? 'N/A' }}</td>
                        <th>City</th>
                        <td>{{ $distributor->city->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>State</th>
                        <td>{{ $distributor->state->state ?? 'N/A' }}</td>
                        <th>Country</th>
                        <td>India</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card p-4 mt-4 mb-4">
            <h5 class="fw-bold text-uppercase mb-3">Bank Account Details</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Bank Name</th>
                        <td>{{ $distributor->bank_name ?? "N/A" }}</td>
                        <th>Account Holder</th>
                        <td>{{ $distributor->account_holder_name ?? "N/A" }}</td>
                    </tr>
                    <tr>
                        <th>IFSC Code</th>
                        <td>{{ $distributor->ifsc_code ?? "N/A"  }}</td>
                        <th>Account Number</th>
                        <td>{{ $distributor->account_number ?? "N/A"  }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </section>
</main>

@push('styles')
    <style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        width: 25%;
    }

    .table td {
        vertical-align: middle;
    }

    .card h5 {
        font-size: 18px;
    }

    .badge {
        padding: 6px 12px;
        font-size: 13px;
    }
    .table td.remark-cell {
        max-width: 400px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

@endpush
@endsection
