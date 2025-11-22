@extends('layouts.main')
@section('title', 'Singhal - Dispatch Details')

@section('content')
<main id="main" class="main">
    <div class="container-fluid my-4">
        <!-- Header and Breadcrumb -->
        <div class="glass-card d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-primary text-white me-3">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <h2 class="mb-0">Dispatch Details - {{ $dispatch->dispatch_number ?? 'N/A' }}</h2>
                    <small class="text-muted">Detailed view of dispatch entry</small>
                </div>
            </div>
            <a href="{{ route('dispatch.index') }}" class="btn btn-outline-primary">
                <i class="fa fa-arrow-left me-1"></i> Back to Dispatch List
            </a>
        </div>

        <section class="section">
            <div class="row g-4 mt-2">
                <!-- Party Information -->
                <div class="col-lg-6">
                    <div class="glass-card">
                        <div class="card-title">
                            <i class="fas fa-user-circle text-primary"></i> Party Information
                        </div>
                        <div class="basic-details-grid">
                            <div class="detail-item">
                                <label>Name</label>
                                <span>{{ $dispatch->type == 'distributor' && $dispatch->distributor ? $dispatch->distributor->name : ($dispatch->dealer ? $dispatch->dealer->name : 'N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Code</label>
                                <span>{{ $dispatch->type == 'distributor' && $dispatch->distributor ? $dispatch->distributor->code : ($dispatch->dealer ? $dispatch->dealer->code : 'N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Mobile</label>
                                <span>{{ $dispatch->type == 'distributor' && $dispatch->distributor ? $dispatch->distributor->mobile_no : ($dispatch->dealer ? $dispatch->dealer->mobile_no : 'N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Email Address</label>
                                <span>{{ $dispatch->type == 'distributor' && $dispatch->distributor ? $dispatch->distributor->email : ($dispatch->dealer ? $dispatch->dealer->email : 'N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <label>GST No.</label>
                                <span>{{ $dispatch->type == 'distributor' && $dispatch->distributor ? $dispatch->distributor->gst_num : ($dispatch->dealer ? $dispatch->dealer->gst_num : 'N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <label>PAN</label>
                                <span>{{ $dispatch->type == 'distributor' && $dispatch->distributor ? $dispatch->distributor->pan_num : ($dispatch->dealer ? $dispatch->dealer->pan_num : 'N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Order Type</label>
                                <span class="text-capitalize">{{ $dispatch->type ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Order Limit (MT)</label>
                                <span class="text-danger">{{ $dispatch->type == 'distributor' && $dispatch->distributor ? $dispatch->distributor->order_limit : ($dispatch->dealer ? $dispatch->dealer->order_limit : 'N/A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing and Delivery Address -->
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="glass-card h-100">
                                <div class="card-title">
                                    <i class="fas fa-file-invoice-dollar text-primary"></i> Billing Address
                                </div>
                                <div class="basic-details-grid">
                                    <div class="detail-item">
                                        <label>Recipient Name</label><span>{{ $dispatch->recipient_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Address</label><span>{{ $dispatch->recipient_address ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>State</label><span>{{ $dispatch->recipientState ? $dispatch->recipientState->state : 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>City</label><span>{{ $dispatch->recipientCity ? $dispatch->recipientCity->name : 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Pincode</label><span>{{ $dispatch->recipient_pincode ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="glass-card h-100">
                                <div class="card-title">
                                    <i class="fas fa-truck text-success"></i> Delivery Address
                                </div>
                                <div class="basic-details-grid">
                                    <div class="detail-item">
                                        <label>Consignee Name</label><span>{{ $dispatch->consignee_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Address</label><span>{{ $dispatch->consignee_address ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>State</label><span>{{ $dispatch->consigneeState ? $dispatch->consigneeState->state : 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>City</label><span>{{ $dispatch->consigneeCity ? $dispatch->consigneeCity->name : 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Pincode</label><span>{{ $dispatch->consignee_pincode ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Mobile Number</label><span>{{ $dispatch->consignee_mobile_no ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dispatch Details -->
                <div class="col-12">
                    <div class="glass-card">
                        <div class="card-title">
                            <i class="fas fa-receipt text-warning"></i> Dispatch Details
                        </div>
                        <div class="basic-details-grid">
                            <div class="detail-item">
                                <label>Dispatch Number</label><span>{{ $dispatch->dispatch_number ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Dispatch Date</label><span>{{ $dispatch->dispatch_date ? \Carbon\Carbon::parse($dispatch->dispatch_date)->format('d-M-Y') : 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Bill To</label><span>{{ $dispatch->bill_to ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Bill Number</label><span>{{ $dispatch->bill_number ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Dispatch Out Time</label><span>{{ $dispatch->dispatch_out_time ? \Carbon\Carbon::parse($dispatch->dispatch_out_time)->format('h:i A') : 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Payment Slip</label>
                                <span>
                                    @if ($dispatch->payment_slip)
                                        <a href="{{ Storage::url($dispatch->payment_slip) }}" target="_blank" class="text-primary fw-medium">
                                            <i class="fa-solid fa-file-lines me-2"></i>View Payment Slip
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            <div class="detail-item">
                                <label>Warehouse</label><span>{{ $dispatch->warehouse ? $dispatch->warehouse->name : 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Status</label>
                                <span class="badge bg-{{ $dispatch->status == 'Pending' ? 'warning' : 'success' }} text-dark">{{ ucfirst($dispatch->status ?? 'N/A') }}</span>
                            </div>

                             <div class="detail-item">
                                <label>Created By</label>
                                <span>{{ ucfirst($dispatch->created_by ?? 'N/A') }}</span>
                            </div>
                            <div class="detail-item col-span-2">
                                <label>Remark</label><span>{{ $dispatch->dispatch_remarks ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transport & Vehicle Details -->
                <div class="col-12">
                    <div class="glass-card">
                        <div class="card-title">
                            <i class="fas fa-shuttle-van text-success"></i> Transport & Vehicle Details
                        </div>
                        <div class="basic-details-grid">
                            <div class="detail-item">
                                <label>Transporter Name</label><span>{{ $dispatch->transporter_name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Vehicle Number</label><span>{{ $dispatch->vehicle_no ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Driver Name</label><span>{{ $dispatch->driver_name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Driver Mobile</label><span>{{ $dispatch->driver_mobile_no ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>E-Way Bill No.</label><span>{{ $dispatch->e_way_bill_no ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Bilty No.</label><span>{{ $dispatch->bilty_no ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item col-span-2">
                                <label>Transport Remark</label><span>{{ $dispatch->transport_remarks ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item List -->
                <div class="col-12">
                    <div class="glass-card">
                        <div class="card-title">
                            <i class="fa fa-boxes-stacked text-warning"></i> Item List
                        </div>
                        <div class="table-responsive">
                            <table class="table modern-table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order No</th>
                                        <th>Allocation</th>
                                        <th>Item</th>
                                        <th>Size</th>
                                        <th>Ordered (MT)</th>
                                        <th>Already Disp. (MT)</th>
                                        <th>Remaining (MT)</th>
                                        <th>Dispatch Qty (MT)</th>
                                        <th>Basic Price (₹/MT)</th>
                                        <th>Gauge Diff (₹)</th>
                                        <th>Final Price (₹/MT)</th>
                                        <th>Loading Charge (₹)</th>
                                        <th>Insurance (₹)</th>
                                        <th>GST (%)</th>
                                        <th>Token Amount (₹)</th>
                                        <th>Total Amount (₹)</th>
                                        <th>Payment Term</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dispatch->items ?? [] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->order ? $item->order->order_number : 'N/A' }}</td>
                                            <td>
                                                @if ($item->allocation && $item->allocation->allocatedTo)
                                                    @if ($item->allocation->allocated_to_type === 'distributor')
                                                        <strong>Self</strong> –
                                                        {{ $item->allocation->allocatedTo->name }}
                                                        ({{ $item->allocation->allocatedTo->code }})
                                                    @elseif ($item->allocation->allocated_to_type === 'dealer')
                                                        <strong>Dealer</strong> –
                                                        {{ $item->allocation->allocatedTo->name }}
                                                        ({{ $item->allocation->allocatedTo->code }})
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $item->item_name ?? 'N/A' }}</td>
                                            <td>{{ $item->size ? $item->size->size : 'N/A' }} mm</td>
                                            <td>{{ $item->order_qty ? number_format($item->order_qty, 2) : 'N/A' }}</td>
                                            <td>{{ $item->already_disp ? number_format($item->already_disp, 2) : '0.00' }}</td>
                                            <td>{{ $item->remaining_qty ? number_format($item->remaining_qty, 2) : 'N/A' }}</td>
                                            <td>{{ $item->dispatch_qty ? number_format($item->dispatch_qty, 2) : 'N/A' }}</td>
                                            <td>{{ $item->basic_price ? number_format($item->basic_price, 2) : 'N/A' }}</td>
                                            <td>{{ $item->gauge_diff ? number_format($item->gauge_diff, 2) : 'N/A' }}</td>
                                            <td>{{ $item->final_price ? number_format($item->final_price, 2) : 'N/A' }}</td>
                                            <td>{{ $item->loading_charge ? number_format($item->loading_charge, 2) : 'N/A' }}</td>
                                            <td>{{ $item->insurance ? number_format($item->insurance, 2) : 'N/A' }}</td>
                                            <td>{{ $item->gst ? number_format($item->gst, 2) : 'N/A' }}</td>
                                            <td>{{ $item->token_amount ? number_format($item->token_amount, 2) : 'N/A' }}</td>
                                            <td>{{ $item->total_amount ? number_format($item->total_amount, 2) : 'N/A' }}</td>
                                            <td>{{ $item->payment_term ?? 'N/A' }}</td>
                                            <td>{{ $item->remark ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                    @if (empty($dispatch->items) || $dispatch->items->isEmpty())
                                        <tr>
                                            <td colspan="20" class="text-center">No items available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="col-12">
                    <div class="glass-card">
                        <div class="card-title">
                            <i class="fas fa-file-contract text-primary"></i> Terms & Conditions
                        </div>
                        <div class="terms-content">
                            {!! $dispatch->terms_conditions ?? 'N/A' !!}
                        </div>
                    </div>
                </div>

                <!-- Attachments and Order Summary -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="glass-card">
                            <div class="card-title">
                                <i class="fa-solid fa-paperclip text-primary"></i> Attachments
                            </div>
                            <div class="table-responsive">
                                <table class="table modern-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>File</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dispatch->attachments ?? [] as $attachment)
                                            <tr>
                                                <td>
                                                    <a href="{{ $attachment->document ? Storage::url($attachment->document) : '#' }}" class="text-primary fw-medium" target="_blank">
                                                        <i class="fa-solid fa-file-lines me-2"></i>{{ $attachment->document ? basename($attachment->document) : 'N/A' }}
                                                    </a>
                                                </td>
                                                <td>{{ $attachment->remark ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                        @if (empty($dispatch->attachments) || $dispatch->attachments->isEmpty())
                                            <tr>
                                                <td colspan="2" class="text-center">No attachments available</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="glass-card">
                            <div class="card-title">
                                <i class="fas fa-calculator text-success"></i> Order Summary
                            </div>
                            <div class="basic-details-grid">
                                <div class="detail-item">
                                    <label>Total Items</label><span>{{ $dispatch->items ? $dispatch->items->count() : '0' }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Total Dispatch Qty</label><span>{{ $dispatch->items ? number_format($dispatch->items->sum('dispatch_qty'), 2) : '0.00' }} MT</span>
                                </div>
                                <div class="detail-item">
                                    <label>Additional Charges</label><span>₹{{ $dispatch->additional_charges ? number_format($dispatch->additional_charges, 2) : '0.00' }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Total Amount</label><span class="text-success fw-bold">₹{{ $dispatch->total_amount ? number_format($dispatch->total_amount, 2) : '0.00' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('styles')
<style>
/* ==== GLOBAL ==== */
body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    background: #f9fafb;
    color: #1f2937;
    line-height: 1.6;
}

/* ==== HEADINGS ==== */
h2 {
    color: #111827;
    font-weight: 600;
    font-size: 22px;
    margin-bottom: 8px;
}

/* ==== GLASS CARD ==== */
.glass-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,200,200,0.25);
    transition: all 0.3s ease-in-out;
    margin-bottom: 24px;
}
.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
}

/* ==== ICON CIRCLE ==== */
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* ==== DETAILS GRID ==== */
.basic-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px 30px;
}
.detail-item.col-span-2 {
    grid-column: span 2;
}
.detail-item label {
    display: block;
    font-weight: 600;
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 4px;
}
.detail-item span {
    font-size: 14px;
    font-weight: 500;
    color: #111827;
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 6px;
}

/* ==== CARD TITLE ==== */
.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
}
.card-title i {
    margin-right: 8px;
    font-size: 18px;
}

/* ==== TABLE ==== */
.modern-table thead {
    background-color: #f3f4f6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
}
.modern-table tbody tr {
    transition: background 0.2s;
}
.modern-table tbody tr:hover {
    background-color: #eef2f7;
}
.modern-table td, .modern-table th {
    font-size: 14px;
    vertical-align: middle;
    padding: 12px;
}

/* ==== BADGE ==== */
.badge {
    font-size: 12px;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 20px;
}

/* ==== TERMS CONTENT ==== */
.terms-content {
    background-color: #f8f9fa;
    padding: 16px;
    border-radius: 6px;
    font-size: 14px;
    line-height: 1.6;
}
</style>
@endpush




