@extends('layouts.main')
@section('title', 'Distributor Team Details')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <div class="card team-header-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="card-title">Distributor Team</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('distributor_team.index') }}">Distributor Team</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('distributor_team.index') }}" class="btn btn-back">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card detail-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0 fw-bold">Distributor Team Details</h5>
            </div>

            <table class="table table-custom">
                <tbody>
                    <tr>
                        <th>Distributor ID</th>
                        <td>{{ $team->distributor->code ?? 'N/A' }}</td>
                        <th>Distributor Name</th>
                        <td>{{ $team->distributor->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Assigned Dealers</th>
                        <td>{{ $currentDealers->count() }} Dealers</td>
                        <th>State</th>
                        <td>{{ $team->distributor->state->state ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Contact No.</th>
                        <td>{{ $team->distributor->mobile_no ?? 'N/A' }}</td>
                        <th>Email</th>
                        <td><a href="mailto:{{ $team->distributor->email }}" class="text-primary">{{ $team->distributor->email }}</a></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $team->status === 'Active' ? 'success' : 'secondary' }}">
                                {{ $team->status }}
                            </span>
                        </td>
                        <th>Last Updated</th>
                        <td>{{ \Carbon\Carbon::parse($team->updated_at)->format('j F, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Remark</th>
                        <td colspan="3" class="remark-cell">
                            <textarea name="remarks" disabled id="" cols="30" rows="2" class="form-control bg-light" disabled>{{ $team->remarks ?? 'N/A' }}</textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card detail-card p-4 mb-4">
            <h5 class="card-title mb-3 fw-bold">Current Dealer Members</h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Dealer ID</th>
                            <th>Dealer Name</th>
                            <th>State</th>
                            <th>Order Limit (MT)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($currentDealers as $dealer)
                            <tr>
                                <td>{{ $dealer->code ?? 'N/A' }}</td>
                                <td>{{ $dealer->name }}</td>
                                <td>{{ $dealer->state->state ?? 'N/A' }}</td>
                                <td>{{ $dealer->order_limit }} MT</td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No Current Dealers</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card detail-card p-4 mb-4">
            <h5 class="card-title mb-3 fw-bold">Past Dealer Members</h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Dealer ID</th>
                            <th>Dealer Name</th>
                            <th>State</th>
                            <th>Order Limit (MT)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pastDealers as $dealer)
                            <tr>
                                <td>{{ $dealer->code ?? 'N/A' }}</td>
                                <td>{{ $dealer->name }}</td>
                                <td>{{ $dealer->state->state ?? 'N/A' }}</td>
                                <td>{{ $dealer->order_limit }} MT</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $dealer->pivot->status ?? 'Inactive' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No Past Dealers</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

@push('styles')
<style>
    .table-custom th {
        background-color: #f8f9fa;
        font-weight: 600;
        width: 20%;
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

    .detail-card .card-title {
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

    .team-header-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 12px 20px;
        margin-bottom: 20px;
    }

    .team-header-card .card-body {
        padding: 0;
    }

    .team-header-card .card-title {
        font-size: 24px;
        font-weight: 600;
        color: #1a2e44;
        margin: 0;
    }

    .team-header-card .breadcrumb {
        background: transparent;
        font-size: 14px;
        margin-top: 4px;
    }

    .team-header-card .breadcrumb-item a {
        color: #1a2e44;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .team-header-card .breadcrumb-item a:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    .team-header-card .breadcrumb-item.active {
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
