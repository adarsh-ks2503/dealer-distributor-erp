@extends('layouts.main')
@section('title', 'View Item Size - Singhal')

@push('styles')
<style>
    /* General Styling */
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: none;
        transition: all 0.3s ease-in-out;
        opacity: 0; /* Initially hidden for animation */
        transform: translateY(20px); /* Initially moved down for animation */
        animation: fadeInUp 0.5s ease-out forwards;
    }

    /* Animation Keyframes */
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Staggering card animations */
    .card:nth-of-type(1) {
        animation-delay: 0.1s;
    }
    .card:nth-of-type(2) {
        animation-delay: 0.2s;
    }


    .card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .card-title {
        font-weight: 600;
        color: #012970;
        padding-bottom: 15px;
    }

    /* View Details Table */
    .view-details-table td {
        padding: 12px 15px;
        font-size: 1rem;
    }

    .view-details-table tr:not(:last-child) {
        border-bottom: 1px solid #eef0f2;
    }

    .view-details-table td:first-child {
        font-weight: 600;
        color: #555;
        width: 30%;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .status-approved {
        background-color: #d1f7e5;
        color: #12804b;
    }

    .status-rejected {
        background-color: #ffe6e6;
        color: #d92d2d;
    }

    /* History Table */
    .history-table thead th {
        background-color: #f6f9ff;
        color: #012970;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .history-table tbody tr {
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .history-table tbody tr:hover {
        background-color: #f6f9ff;
        transform: scale(1.015); /* Slightly enlarges the row on hover */
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    /* Back Button */
    .btn-back {
        transition: all 0.3s ease;
    }
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

</style>
@endpush

@section('content')
<main id="main" class="main">

    {{-- Session Messages (You can keep your existing ones) --}}

    <div class="dashboard-header pagetitle">
        <h1>Item Size</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Item Master</a></li>
                    <li class="breadcrumb-item">Item Size</li>
                </ol>
            </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            {{-- Main View Card --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-4">
                        <h5 class="card-title">View - {{ $itemSize->itemName->item_name }} {{ $itemSize->size }} mm</h5>
                        <table class="table table-borderless view-details-table">
                            <tbody>
                                <tr>
                                    <td>Item</td>
                                    <td><strong>{{ $itemSize->itemName->item_name }}</strong></td>
                                </tr>
                                <tr>
                                    <td>HSN Code</td>
                                    <td>{{ $itemSize->hsn_code }}</td>
                                </tr>
                                <tr>
                                    <td>Size (mm)</td>
                                    <td>{{ $itemSize->size }} mm</td>
                                </tr>
                                <tr>
                                    <td>Rate</td>
                                    <td>₹{{ $itemSize->rate }}</td>
                                </tr>
                                <tr>
                                    <td>Approval Time</td>
                                    <td>{{ \Carbon\Carbon::parse($itemSize->approval_time ?? '2025-08-28')->format('d M, Y - H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td>Approval Status</td>
                                    <td>
                                        <span class="status-badge status-approved">{{ $itemSize->status ?? 'Active' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Approved By</td>
                                    <td>{{ $itemSize->approved_by ?? 'Admin' }}</td>
                                </tr>
                                <tr>
                                    <td>Remarks</td>
                                    <td>{{ $itemSize->remarks ?? "N/A" }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Price Change History Card --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-4">
                        <h5 class="card-title">Today's Item Basic Price Change Details</h5>
                        <div class="table-responsive">
                            <table class="table table-striped history-table text-center">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Size-Rate Change Date & Time</th>
                                        <th scope="col">Rate</th>
                                        <th scope="col">Remarks</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Changed By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Dummy Data - Replace with a @foreach loop --}}
                                    @foreach($priceHistory as $i=>$history)
                                        <tr>
                                            <td>{{ $i+1 }}</td>
                                            <td>{{ $history->approval_time->format('j F, Y h:i A') }}</td>
                                            <td>{{ $history->rate }}</td>
                                            <td>{{ $history->remarks }}</td>
                                            <td><span class="status-badge status-{{ $history->status == 'Rejected' ? 'rejected' : 'approved' }}">{{ $history->status }}</span></td>
                                            <td>{{ $history->approved_by }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Back Button --}}
            <div class="col-12 text-center mt-3">
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-back"><i class="bi bi-arrow-left"></i> Back</a>
            </div>
        </div>
    </section>

</main>@endsection
