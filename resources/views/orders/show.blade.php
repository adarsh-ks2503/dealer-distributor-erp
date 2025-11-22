@extends('layouts.main')
@section('title', 'Order Details')

@section('content')
<main id="main" class="main">

    {{-- Page Header --}}
    <div class="card shadow-sm p-3">
        <div class="pagetitle d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-receipt-cutoff me-2"></i> Order Details</h1>
            <a href="{{ route('order_management') }}" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <section class="section">

        {{-- Order Basic Details --}}
        <div class="card shadow-sm border-0 p-4 mb-4 card-hover">
            <h5 class="fw-bold text-uppercase mb-3">
                <i class="bi bi-info-circle me-2 text-primary"></i> Basic Information
            </h5>
            <table class="table table-bordered align-middle">
                <tbody>
                    <tr>
                        <th>Order No.</th>
                        <td>{{ $order->order_number }}</td>
                        <th>Date</th>
                        <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Placed By</th>
                        <td>
                            @if($order->type === 'dealer')
                                <i class="bi bi-person-badge text-success"></i>
                                Dealer - {{ $order->dealer->name ?? 'N/A' }}
                            @else
                                <i class="bi bi-building text-info"></i>
                                Distributor - {{ $order->distributor->name ?? 'N/A' }}
                            @endif
                        </td>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $order->status === 'approved' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'partial dispatch' ? 'info':'secondary')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ $order->created_by }}</td>
                        <th>Overall Remark</th>
                        <td><textarea class="form-control" aria-label="With textarea">{{ $order->remarks ?? 'N/A' }}</textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Status Change Details --}}
        @if($order->status_changed_at)
            <div class="card shadow-sm border-0 p-4 mb-4 card-hover">
                <h5 class="fw-bold text-uppercase mb-3">
                    <i class="bi bi-clock-history me-2 text-info"></i> Status Change Details
                </h5>
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th width="25%">Changed By</th>
                            <td>
                                <i class="bi bi-person-check text-success me-2"></i>
                                <strong>{{ $order->status_changed_by ?? '—' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Changed On</th>
                            <td>
                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                {{ \Carbon\Carbon::parse($order->status_changed_at)->format('d M, Y \a\t h:i A') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Remarks</th>
                            <td>
                                @if($order->status_change_remarks)
                                    <div class="remarks-container p-3 bg-light rounded">
                                        <textarea class="mb-0 text-muted col-12" readonly>{!! nl2br(e($order->status_change_remarks)) !!}</textarea>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Charges --}}
        <div class="card shadow-sm border-0 p-4 mb-4 card-hover">
            <h5 class="fw-bold text-uppercase mb-3">
                <i class="bi bi-cash-coin me-2 text-success"></i> Charges & Payments
            </h5>
            <table class="table table-bordered align-middle">
                <tbody>
                    <tr>
                        <th>Loading Charge</th>
                        <td>₹{{ number_format($order->loading_charge, 2) }}</td>
                        <th>Insurance Charge</th>
                        <td>₹{{ number_format($order->insurance_charge, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Token Amount</th>
                        <td colspan="3">₹{{ \App\Helpers\NumberHelper::formatIndianCurrency($order->allocations->sum('token_amount') ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Allocations --}}
        <div class="card shadow-sm border-0 p-4 mb-4 card-hover">
            <h5 class="fw-bold text-uppercase mb-3">
                <i class="bi bi-box-seam me-2 text-warning"></i> Allocations
            </h5>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Allocated To</th>
                            <th>Qty</th>
                            <th>Basic Price</th>
                            <th>Agreed Price</th>
                            <th>Token Amount</th>
                            <th>Payment Term</th>
                            <th>Dispatched Qty</th>
                            <th>Remaining Qty</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->allocations as $i => $alloc)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>
                                    {{ ucfirst($alloc->allocated_to_type) }} -
                                    {{ $alloc->allocated_to_type === 'dealer'
                                        ? (\App\Models\Dealer::find($alloc->allocated_to_id)->name ?? 'N/A')
                                        : (\App\Models\Distributor::find($alloc->allocated_to_id)->name ?? 'N/A') }}
                                </td>
                                <td>{{ $alloc->qty }}</td>
                                <td>₹{{ number_format($alloc->basic_price, 2) }}</td>
                                <td>₹{{ number_format($alloc->agreed_basic_price, 2) }}</td>
                                <td>₹{{ number_format($alloc->token_amount ?? 0, 2) }}</td>
                                <td>{{ $alloc->payment_terms ?? '—' }}</td>
                                <td>{{ $alloc->dispatched_qty }}</td>
                                <td>{{ $alloc->remaining_qty }}</td>
                                <td>{{ $alloc->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">No allocations found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Attachments --}}
        <div class="card shadow-sm border-0 p-4 mb-4 card-hover">
            <h5 class="fw-bold text-uppercase mb-3">
                <i class="bi bi-paperclip me-2 text-secondary"></i> Attachments
            </h5>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Attachment</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->attachments as $file)
                            <tr>
                                <td>
                                    @php
                                        $extension = pathinfo($file->attachment, PATHINFO_EXTENSION);
                                        $iconClass = match(strtolower($extension)) {
                                            'pdf' => 'bi-file-earmark-pdf text-danger',
                                            'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image text-primary',
                                            'doc', 'docx' => 'bi-file-earmark-word text-primary',
                                            default => 'bi-file-earmark-text text-secondary'
                                        };
                                    @endphp
                                    <a href="{{ Storage::url($file->attachment) }}" target="_blank" class="text-decoration-none attachment-link">
                                        <i class="bi {{ $iconClass }} me-2"></i>
                                        <span class="text-truncate" style="max-width: 300px;" title="{{ basename($file->attachment) }}">
                                            {{ basename($file->attachment) }}
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    @if($file->remarks)
                                        <div class="remarks-container">
                                            <p class="remarks-text">{!! nl2br(e($file->remarks)) !!}</p>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">
                                    <i class="bi bi-file-earmark-x me-2"></i>No attachments available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Terms & Conditions --}}
        <div class="card shadow-sm border-0 p-4 mb-4 card-hover">
            <h5 class="fw-bold text-uppercase mb-3">
                <i class="bi bi-journal-text me-2 text-info"></i> Terms And Conditions
            </h5>
            <div>
                <strong>Terms & Conditions:</strong>
                {!! $order->terms_conditions !!}
            </div>
        </div>

    </section>
</main>

@push('styles')
<style>
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
    }
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge {
        padding: 6px 10px;
        font-size: 13px;
    }
    .attachment-link {
        color: #007bff;
        transition: color 0.2s ease;
    }
    .attachment-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    .remarks-container {
        background: #f8f9fa;
        padding: 8px;
        border-radius: 6px;
        min-height: 50px;
        display: flex;
        align-items: center;
    }
    .remarks-text {
        margin: 0;
        font-size: 14px;
        line-height: 1.5;
        color: #333;
    }
</style>
@endpush
@endsection
