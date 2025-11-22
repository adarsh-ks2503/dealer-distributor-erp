@extends('layouts.main')
@section('title', 'Singhal - Create Dispatch')
@section('content')
<main class="main" id="main">
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="container-fluid my-4">
        <div class="card mb-4 pt-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Create Dispatch - {{ $orderType }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Dispatch</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('dispatch.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
        <form action="{{ route('dispatch.store') }}" method="POST" enctype="multipart/form-data" id="dispatch-form">
            @csrf
            <input type="hidden" name="type" value="{{ $orderType }}">
            @if ($orderType == 'distributor')
                <input type="hidden" name="distributor_id" value="{{ $party->id }}">
            @else
                <input type="hidden" name="dealer_id" value="{{ $party->id }}">
            @endif
            <div class="row">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-user-circle me-2"></i>Basic Info</div>
                        <div class="card-body">
                            <div class="basic-details-grid">
                                <div class="detail-item"><label>Name</label><span>{{ $party->name }}</span></div>
                                <div class="detail-item"><label>Code</label><span>{{ $party->code }}</span></div>
                                <div class="detail-item"><label>Mobile</label><span>{{ $party->mobile_no }}</span></div>
                                <div class="detail-item"><label>Email Address</label><span>{{ $party->email }}</span></div>
                                <div class="detail-item"><label>GST No.</label><span>{{ $party->gst_num }}</span></div>
                                <div class="detail-item"><label>PAN</label><span>{{ $party->pan_num }}</span></div>
                                <div class="detail-item"><label>Order Type</label><span>{{ $orderType }}</span></div>
                                <div class="detail-item"><label>Order Limit (MT)</label><span class="text-danger">{{ $party->order_limit }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100"><div class="card-header"><i class="fas fa-file-invoice-dollar me-2"></i>Billing Address <span class="required-asterisk">*</span></div>
                                <div class="card-body d-flex flex-column gap-3 mt-2">
                                    <input required id="recipient-name" name="recipient_name" type="text" class="form-control" placeholder="Recipient Name" value="{{ old('recipient_name', $party->name) }}">
                                    <textarea required name="recipient_address" class="form-control" rows="2" placeholder="Billing Address">{{ old('recipient_address', $party->address) }}</textarea>
                                    <select required name="recipient_state" id="state" class="form-select">
                                        <option value="">Select</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}" {{ old('recipient_state', $party->state_id) == $state->id ? 'selected' : '' }}>{{ $state->state }}</option>
                                        @endforeach
                                    </select>
                                    <select required name="recipient_city" class="form-select" id="city">
                                        <option value="">Select City</option>
                                    </select>
                                    <div>
                                        <input required name="recipient_pincode" type="text" class="form-control" placeholder="Pincode" maxlength="6" value="{{ old('recipient_pincode', $party->pincode) }}">
                                        <span id="recipient-pincode-error" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100"><div class="card-header"><i class="fas fa-truck me-2"></i>Delivery Address <span class="required-asterisk">*</span></div>
                                <div class="card-body d-flex flex-column gap-3 mt-2">
                                    <input required name="consignee_name" id="consignee-name" type="text" class="form-control" placeholder="Consignee Name" value="{{ old('consignee_name', $party->name) }}">
                                    <textarea required name="consignee_address" class="form-control" rows="2" placeholder="Delivery Address">{{ old('consignee_address', $party->address) }}</textarea>
                                    <select required name="consignee_state" class="form-select" id="state2">
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}" {{ old('consignee_state', $party->state_id) == $state->id ? 'selected' : '' }}>{{ $state->state }}</option>
                                        @endforeach
                                    </select>
                                    <select required name="consignee_city" class="form-select" id="city2">
                                        <option value="">Select City</option>
                                    </select>
                                    <div>
                                        <input required name="consignee_pincode" type="text" class="form-control" placeholder="Pincode" maxlength="6" value="{{ old('consignee_pincode', $party->pincode) }}">
                                        <span id="consignee-pincode-error" class="text-danger"></span>
                                    </div>
                                    <input maxlength="15" name="consignee_mobile_no" type="text" class="form-control" placeholder="Mobile Number" value="{{ old('consignee_mobile_no', $party->mobile_no) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header"><i class="fas fa-receipt me-2"></i>Dispatch Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Warehouse <span class="required-asterisk">*</span></label>
                            <select required name="warehouse_id" id="warehouse_id" class="form-select">
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dispatch Number</label>
                            <input id="dispatch_number" name="dispatch_number" type="text" class="form-control" readonly value="{{ old('dispatch_number', 'Auto-Generated') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dealer/Distributor Name</label>
                            <input type="text" class="form-control" readonly value="{{ $party->name }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dispatch Date <span class="required-asterisk">*</span></label>
                            <input type="date" id="dispatch_date" name="dispatch_date" class="form-control" required value="{{ old('dispatch_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bill To</label>
                            <input name="bill_to" id="bill-to" type="text" class="form-control" placeholder="Consignee Name" value="{{ old('bill_to', $party->name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bill No</label>
                            <input name="bill_number" id="bill-number" type="text" class="form-control" placeholder="Bill Number" value="{{ old('bill_number') }}">
                            <span id="bill-number-error" class="text-danger"></span>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dispatch Out Time</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="dispatch_out_time_display"
                                    id="dispatch_out_time_display"
                                    class="form-control"
                                    placeholder="Select time (e.g. 02:30 PM)"
                                    value="{{ old('dispatch_out_time_display') ? \Carbon\Carbon::createFromFormat('H:i', old('dispatch_out_time'))->format('h:i A') : '' }}"
                                    autocomplete="off"
                                >
                                <span class="input-group-text">
                                    <i class="fas fa-clock"></i>
                                </span>
                            </div>
                            <input type="hidden" name="dispatch_out_time" id="dispatch_out_time_hidden">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Slip</label>
                            <div class="input-group">
                                <label for="paymentSlip" class="custom-file-upload w-100">
                                    <i class="fas fa-cloud-upload-alt me-1"></i>
                                    <span class="file-label">Choose file</span>
                                    <span class="file-name text-muted ms-2"></span>
                                </label>
                                <input name="payment_slip" type="file" id="paymentSlip" class="attachment-input">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Remark</label>
                            <textarea name="dispatch_remarks" class="form-control" rows="2">{{ old('dispatch_remarks') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header"><i class="fas fa-shuttle-van me-2"></i>Transport & Vehicle Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Transporter Name </label>
                            <input name="transporter_name" type="text" class="form-control" value="{{ old('transporter_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vehicle No.</label>
                            <input name="vehicle_no" type="text" class="form-control" value="{{ old('vehicle_no') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Driver Name</label>
                            <input name="driver_name" type="text" class="form-control" value="{{ old('driver_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Driver Mobile</label>
                            <input maxlength="15" name="driver_mobile_no" type="text" class="form-control" value="{{ old('driver_mobile_no') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">E-Way Bill No.</label>
                            <input name="e_way_bill_no" type="text" class="form-control" value="{{ old('e_way_bill_no') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bilty No.</label>
                            <input name="bilty_no" type="text" class="form-control" value="{{ old('bilty_no') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remark</label>
                            <input name="transport_remarks" type="text" class="form-control" value="{{ old('transport_remarks') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list-ul me-2"></i>Item List</span>
                    <button type="button" class="btn btn-sm btn-success" id="add-item-row"><i class="fas fa-plus me-1"></i> Add Item</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Order No. <span class="required-asterisk">*</span></th>
                                    <th>Allocation <span class="required-asterisk">*</span></th>
                                    <th>Order QTY (MT)</th>
                                    <th>Already Disp.</th>
                                    <th>Remaining QTY</th>
                                    <th>Item Name</th>
                                    <th>Size <span class="required-asterisk">*</span></th>
                                    {{-- <th>Length (ft) <span class="required-asterisk">*</span></th> --}}
                                    <th>Dispatch Qty <span class="required-asterisk">*</span></th>
                                    <th>Order Basic Price</th>
                                    <th>Gauge Diff (Rate)</th>
                                    <th>Final Price /MT</th>
                                    <th>Loading Charge</th>
                                    <th>Insurance</th>
                                    <th>GST (%)</th>
                                    <th>Token Amount (₹)</th>
                                    <th>Total Amount (₹)</th>
                                    <th>Payment Term</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="item-list-container">
                                <tr>
                                    <td><span class="sn">1</span></td>
                                    <td>
                                        <select required name="items[0][order_id]" class="form-select form-select-sm order-select">
                                            <option value="">Select</option>
                                            @foreach ($orders as $order)
                                                <option value="{{ $order->id }}" {{ old('items.0.order_id') == $order->id ? 'selected' : '' }}>{{ $order->order_number }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select required name="items[0][allocation_id]" class="form-select form-select-sm allocation-select">
                                            <option value="">Select</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="items[0][order_qty]" class="form-control form-control-sm" readonly value="{{ old('items.0.order_qty') }}"></td>
                                    <td><input type="text" name="items[0][already_disp]" class="form-control form-control-sm" readonly value="{{ old('items.0.already_disp') }}"></td>
                                    <td><input type="text" name="items[0][remaining_qty]" class="form-control form-control-sm remaining-qty" readonly value="{{ old('items.0.remaining_qty') }}"></td>
                                    <td><input type="text" name="items[0][item_name]" class="form-control form-control-sm" value="{{ old('items.0.item_name', $singleItemName ?? 'TMT Bar') }}" readonly></td>
                                    <td>
                                        <select required name="items[0][size]" class="form-select form-select-sm size-select">
                                            <option value="">Select</option>
                                            @foreach ($sizes as $size)
                                                <option data-rate="{{ $size->rate }}" value="{{ $size->id }}" {{ old('items.0.size') == $size->id ? 'selected' : '' }}>{{ $size->size }} mm</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    {{-- <td><input type="number" step="0.01" value="{{ old('items.0.length', '12') }}" name="items[0][length]" class="form-control form-control-sm" placeholder="Enter"></td> --}}
                                    <td><input required type="number" step="0.01" name="items[0][dispatch_qty]" class="form-control form-select-sm dispatch-qty item-calc" placeholder="Enter" value="{{ old('items.0.dispatch_qty') }}"></td>
                                    <td><input readonly type="number" step="0.01" name="items[0][basic_price]" class="form-control form-control-sm item-calc basic-price-input" value="{{ old('items.0.basic_price') }}"></td>
                                    <td><input type="number" step="0.01" name="items[0][gauge_diff]" class="form-control form-control-sm item-calc" readonly value="{{ old('items.0.gauge_diff') }}"></td>
                                    <td><input type="text" name="items[0][final_price]" class="form-control form-control-sm" placeholder="Auto Calc" readonly value="{{ old('items.0.final_price') }}"></td>
                                    <td><input type="number" step="0.01" name="items[0][loading_charge]" class="form-control form-control-sm item-calc" value="{{ old('items.0.loading_charge', $loadingCharge ?? 265) }}" readonly></td>
                                    <td><input type="number" step="0.01" name="items[0][insurance]" class="form-control form-control-sm item-calc" value="{{ old('items.0.insurance', $insuranceCharge ?? 40) }}" readonly></td>
                                    <td><input type="number" step="0.01" name="items[0][gst]" class="form-control form-control-sm item-calc" value="{{ old('items.0.gst', $gstRate ?? 18) }}" readonly></td>
                                    <td><input type="text" name="items[0][token_amount]" class="form-control form-control-sm token-amount" readonly value="{{ old('items.0.token_amount', 'N/A') }}"></td>
                                    <td><input type="text" name="items[0][total_amount]" class="form-control form-control-sm item-total" placeholder="Auto Calc" readonly value="{{ old('items.0.total_amount') }}"></td>
                                    <td>
                                        <select name="items[0][payment_term]" class="form-select form-select-sm">
                                            <option value="">Select</option>
                                            <option value="Advance" {{ old('items.0.payment_term') == 'Advance' ? 'selected' : '' }}>Advance</option>
                                            <option value="Next Day" {{ old('items.0.payment_term') == 'Next Day' ? 'selected' : '' }}>Next Day</option>
                                            <option value="15 Days Later" {{ old('items.0.payment_term') == '15 Days Later' ? 'selected' : '' }}>15 Days Later</option>
                                            <option value="30 Days Later" {{ old('items.0.payment_term') == '30 Days Later' ? 'selected' : '' }}>30 Days Later</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="items[0][remark]" class="form-control form-control-sm" placeholder="Remark" value="{{ old('items.0.remark') }}"></td>
                                    <td class="action-cell"><button type="button" class="btn btn-sm btn-danger remove-item-row"><i class="fas fa-trash-alt"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- TERMS & CONDITIONS – REPLACED WITH SUMMERNOTE (only this block is changed) --}}
            <div class="basic-info-card p-4 shadow-sm rounded-3 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle me-2 bg-info">
                        <i class="bi bi-file-earmark-text text-white"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Terms & Conditions</h6>
                </div>
                @php
                    $defaultTerms = <<<HTML
                    <ul>
                        <li>The above rates are basic Ex Plant.</li>
                        <li>Loading charges @ 265/- PMT</li>
                        <li>INSURANCE CHARGES @ 40/- PMT</li>
                        <li>GST 18% extra.</li>
                        <li>Gauge Difference will be extra</li>
                        <li>Freight will be extra</li>
                    </ul>
                    HTML;
                @endphp
                <div class="row">
                    <div class="col-12">
                        <label class="info-label">Terms & Conditions</label>
                        <textarea name="terms_conditions" id="terms_conditions" class="summernote">{!! old('terms_conditions', $defaultTerms) !!}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-paperclip me-2"></i>Attachments</span>
                            <button type="button" class="btn btn-sm btn-success" id="add-attachment-row"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="attachment-container">
                                    <thead>
                                        <tr>
                                            <th>Upload Document <span class="required-asterisk">*</span></th>
                                            <th>Remark</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="attachment-row">
                                            <td>
                                                <div class="input-group">
                                                    <label for="attachment_0" class="custom-file-upload w-100">
                                                        <i class="fas fa-cloud-upload-alt me-1"></i>
                                                        <span class="file-label">Choose file</span>
                                                        <span class="file-name text-muted ms-2"></span>
                                                    </label>
                                                    <input type="file" name="attachments[0][document]" id="attachment_0" class="attachment-input">
                                                </div>
                                            </td>
                                            <td><input type="text" name="attachments[0][remark]" class="form-control" placeholder="Remark" value="{{ old('attachments.0.remark') }}"></td>
                                            <td class="action-cell"><button type="button" class="btn btn-sm btn-danger remove-attachment-row"><i class="fas fa-trash-alt"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-calculator me-2"></i>Order Summary</div>
                        <div class="card-body">
                            <div class="mb-3"><label class="form-label">Additional Charges</label>
                                <input type="number" name="additional_charges" id="additional-charges" class="form-control" placeholder="Enter amount" value="{{ old('additional_charges') }}"></div><hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Total Amount:</h5>
                                <h4 class="mb-0 text-success fw-bold" id="grand-total">₹0.00</h4>
                                <input type="hidden" name="total_amount" id="total-amount-hidden" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end my-4">
                <a href="{{ route('dispatch.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Dispatch</button>
            </div>
        </form>
    </div>
</main>
@endsection
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    body {
        background-color: #f4f7fc;
    }
    .card {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        margin-bottom: 2rem;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .card-header {
        background: #ffffff;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        font-size: 1.1rem;
        color: #435ebe;
        padding: 1rem 1.5rem;
    }
    .form-control, .form-select {
        border-radius: 6px;
    }
    .form-control:disabled, .form-control[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    .btn-primary {
        background-color: #435ebe;
        border-color: #435ebe;
        transition: all 0.2s ease;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
    }
    .btn-primary:hover {
        background-color: #3950a2;
        border-color: #3950a2;
        transform: scale(1.03);
    }
    .basic-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem 1.5rem;
    }
    .detail-item label {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .detail-item span {
        display: block;
        padding: 0.5rem 0.75rem;
        background-color: #f8f9fa;
        border-radius: 6px;
        font-weight: 500;
    }
    .detail-item .text-danger {
        color: #dc3545 !important;
        font-weight: 700;
    }
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
    }
    .table td {
        vertical-align: middle;
    }
    .table input, .table select {
        min-width: 120px;
    }
    .table .action-cell {
        min-width: 60px;
        text-align: center;
    }
    @keyframes fadeIn {
        from { opacity: 0; } to { opacity: 1; }
    }
    .new-row-animation {
        animation: fadeIn 0.5s ease-out;
    }
    .custom-file-upload {
        border: 1px solid #ced4da;
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 0.25rem;
        width: 100%;
        background-color: white;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .custom-file-upload.file-selected {
        border-color: #435ebe;
        background-color: #f0f4ff;
    }
    .custom-file-upload:hover .file-name {
        white-space: normal;
        position: absolute;
        background: #fff;
        border: 1px solid #ced4da;
        padding: 5px;
        z-index: 10;
    }
    input[type="file"] {
        display: none;
    }
    .file-label {
        font-weight: 500;
        color: #495057;
        user-select: none;
    }
    .file-name {
        font-size: 0.9rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .terms-card {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: none;
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }
    .terms-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .terms-card-header {
        background: #435ebe;
        color: white;
        border-radius: 8px 8px 0 0;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.2rem;
        font-weight: 600;
    }
    .terms-card-body {
        padding: 1.5rem;
        background: #fff;
        border-radius: 0 0 8px 8px;
    }
    .terms-card-body textarea {
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 0.95rem;
        line-height: 1.6;
        resize: vertical;
    }
    .terms-card-body label {
        font-weight: 500;
        color: #435ebe;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }
    .terms-card .terms-icon {
        font-size: 1.4rem;
    }
    .is-invalid {
        border-color: #dc3545;
    }
    .text-danger {
        color: #dc3545 !important;
        font-size: 0.9em;
        display: none;
        margin-top: 0.25rem;
    }
    .required-asterisk {
        color: #dc3545 !important;
        font-size: 1em;
        display: inline;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
</style>
@endpush
@push('scripts')
<!-- Summernote (replaces TinyMCE) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.js"></script>
<script>
    $(function () {
        // Initialise Summernote for Terms & Conditions only
        $('#terms_conditions').summernote({
            placeholder: 'Enter terms and conditions...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        // Keep fullscreen background white
        $('#terms_conditions').on('summernote.enterFullscreen', function () {
            $('.note-editor.note-frame.fullscreen').css('background', 'white');
        });
    });
</script>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('dispatch_out_time_display');
    const hiddenInput = document.getElementById('dispatch_out_time_hidden');
    if (!displayInput || !hiddenInput) return;
    // Initialize Flatpickr for time selection in 12-hour format
    flatpickr(displayInput, {
        enableTime: true, // Enable time picker
        noCalendar: true, // Disable date picker (time only)
        dateFormat: "h:i K", // 12-hour format with AM/PM (K = AM/PM)
        time_24hr: false, // 12-hour mode
        minuteIncrement: 1, // Minute steps
        defaultHour: 12, // Default to 12:00 PM
        onChange: function(selectedDates, dateStr, instance) {
            // Convert selected 12-hour time to 24-hour for hidden input
            if (selectedDates.length > 0) {
                const date = selectedDates[0];
                const hours = date.getHours().toString().padStart(2, '0');
                const minutes = date.getMinutes().toString().padStart(2, '0');
                hiddenInput.value = `${hours}:${minutes}`;
                displayInput.classList.remove('is-invalid');
            } else {
                hiddenInput.value = '';
            }
        }
    });
    // Initial load: If old value (24h) exists, convert to 12h and set in Flatpickr
    const initial24 = '{{ old('dispatch_out_time') }}';
    if (initial24) {
        const [hours, minutes] = initial24.split(':').map(Number);
        const period = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        displayInput.value = `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${period}`;
        hiddenInput.value = initial24;
    } else {
        // Pre-fill with current time if no old value
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const period = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        displayInput.value = `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${period}`;
        hiddenInput.value = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    }
    // On manual input (if user types): Validate and convert
    displayInput.addEventListener('input', function () {
        // Flatpickr will handle most, but for safety
        const val = this.value.trim().toUpperCase();
        const match = val.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
        if (match) {
            let [_, h, m, period] = match;
            let hours = parseInt(h);
            const minutes = parseInt(m);
            if (hours === 12) hours = period === 'AM' ? 0 : 12;
            else if (period === 'PM') hours += 12;
            hiddenInput.value = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            this.classList.remove('is-invalid');
        } else if (val === '') {
            hiddenInput.value = '';
            this.classList.remove('is-invalid');
        } else {
            hiddenInput.value = '';
            this.classList.add('is-invalid');
        }
    });
    // Form submit: Ensure valid time
    const form = document.getElementById('dispatch-form');
    form.addEventListener('submit', function (event) {
        if (displayInput.value && !hiddenInput.value) {
            alert('Please select a valid time from the picker.');
            displayInput.focus();
            event.preventDefault();
        }
    });
});
</script>
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('dispatch-form');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        // Helper: Show error
        function showError(input, message) {
            input.classList.add('is-invalid');
            const feedback = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            if (!input.parentNode.querySelector('.invalid-feedback')) {
                input.parentNode.appendChild(feedback);
            }
        }
        // Helper: Clear error
        function clearError(input) {
            input.classList.remove('is-invalid');
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        }
        // Run validation on submit
        form.addEventListener('submit', async function (e) {
            e.preventDefault(); // Prevent default submission until validated
            let isValid = true;
            // 1. Basic fields (type, ids, recipient, consignee, etc.)
            const type = document.querySelector('input[name="type"]');
            if (!type?.value || !['distributor', 'dealer'].includes(type.value)) {
                isValid = false;
                showError(type, 'The dispatch type must be distributor or dealer.');
            } else {
                clearError(type);
            }
            // Distributor/Dealer ID
            if (type?.value === 'distributor') {
                const distId = document.querySelector('input[name="distributor_id"]');
                if (!distId?.value || isNaN(distId.value)) {
                    isValid = false;
                    showError(distId, 'Distributor ID is required and must be an integer.');
                } else {
                    clearError(distId);
                }
            } else if (type?.value === 'dealer') {
                const dealerId = document.querySelector('input[name="dealer_id"]');
                if (!dealerId?.value || isNaN(dealerId.value)) {
                    isValid = false;
                    showError(dealerId, 'Dealer ID is required and must be an integer.');
                } else {
                    clearError(dealerId);
                }
            }
            // Recipient fields
            const recipientName = document.querySelector('input[name="recipient_name"]');
            if (!recipientName?.value || recipientName.value.length > 255) {
                isValid = false;
                showError(recipientName, recipientName.value ? 'Recipient name must not exceed 255 characters.' : 'Recipient name is required.');
            } else {
                clearError(recipientName);
            }
            const recipientAddress = document.querySelector('textarea[name="recipient_address"]');
            if (!recipientAddress?.value || recipientAddress.value.length > 1000) {
                isValid = false;
                showError(recipientAddress, recipientAddress.value ? 'Billing address must not exceed 1000 characters.' : 'Billing address is required.');
            } else {
                clearError(recipientAddress);
            }
            const recipientState = document.querySelector('select[name="recipient_state"]');
            if (!recipientState?.value) {
                isValid = false;
                showError(recipientState, 'Billing state is required.');
            } else {
                clearError(recipientState);
            }
            const recipientCity = document.querySelector('select[name="recipient_city"]');
            if (!recipientCity?.value) {
                isValid = false;
                showError(recipientCity, 'Billing city is required.');
            } else {
                clearError(recipientCity);
            }
            const recipientPin = document.querySelector('input[name="recipient_pincode"]');
            if (!recipientPin?.value || !/^\d{5,6}$/.test(recipientPin.value) || recipientPin.value.length > 10) {
                isValid = false;
                showError(recipientPin, 'Billing pincode must be 5-6 digits.');
            } else {
                clearError(recipientPin);
            }
            // Consignee fields
            const consigneeName = document.querySelector('input[name="consignee_name"]');
            if (!consigneeName?.value || consigneeName.value.length > 255) {
                isValid = false;
                showError(consigneeName, consigneeName.value ? 'Consignee name must not exceed 255 characters.' : 'Consignee name is required.');
            } else {
                clearError(consigneeName);
            }
            const consigneeAddress = document.querySelector('textarea[name="consignee_address"]');
            if (!consigneeAddress?.value || consigneeAddress.value.length > 1000) {
                isValid = false;
                showError(consigneeAddress, consigneeAddress.value ? 'Delivery address must not exceed 1000 characters.' : 'Delivery address is required.');
            } else {
                clearError(consigneeAddress);
            }
            const consigneeState = document.querySelector('select[name="consignee_state"]');
            if (!consigneeState?.value) {
                isValid = false;
                showError(consigneeState, 'Delivery state is required.');
            } else {
                clearError(consigneeState);
            }
            const consigneeCity = document.querySelector('select[name="consignee_city"]');
            if (!consigneeCity?.value) {
                isValid = false;
                showError(consigneeCity, 'Delivery city is required.');
            } else {
                clearError(consigneeCity);
            }
            const consigneePin = document.querySelector('input[name="consignee_pincode"]');
            if (!consigneePin?.value || !/^\d{5,6}$/.test(consigneePin.value) || consigneePin.value.length > 10) {
                isValid = false;
                showError(consigneePin, 'Delivery pincode must be 5-6 digits.');
            } else {
                clearError(consigneePin);
            }
            const consigneeMobile = document.querySelector('input[name="consignee_mobile_no"]');
            if (consigneeMobile?.value && (!/^\d{10,15}$/.test(consigneeMobile.value) || consigneeMobile.value.length > 15)) {
                isValid = false;
                showError(consigneeMobile, 'Consignee mobile must be 10-15 digits.');
            } else {
                clearError(consigneeMobile);
            }
            // Dispatch details
            const dispatchNumber = document.querySelector('input[name="dispatch_number"]');
            if (!dispatchNumber?.value) {
                isValid = false;
                showError(dispatchNumber, 'Dispatch number is required.');
            } else {
                clearError(dispatchNumber);
            }
            const dispatchDate = document.querySelector('input[name="dispatch_date"]');
            const today = new Date().setHours(0, 0, 0, 0);
            const warehouseSelect = document.querySelector('select[name="warehouse_id"]');
            if (!warehouseSelect?.value) {
                isValid = false;
                showError(warehouseSelect, 'Warehouse is required.');
            } else {
                clearError(warehouseSelect);
            }
            const billTo = document.querySelector('input[name="bill_to"]');
            if (billTo?.value && billTo.value.length > 255) {
                isValid = false;
                showError(billTo, 'Bill to must not exceed 255 characters.');
            } else {
                clearError(billTo);
            }
            const billNumber = document.querySelector('#bill-number');
            // Async bill number uniqueness check
            if (billNumber?.value) {
                try {
                    const response = await fetch(`/check-bill-number?bill_number=${encodeURIComponent(billNumber.value)}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.exists) {
                        isValid = false;
                        showError(billNumber, 'Bill number is already in use.');
                    } else {
                        clearError(billNumber);
                    }
                } catch (err) {
                    isValid = false;
                    showError(billNumber, 'Error checking bill number.');
                }
            }
            const dispatchOutTime = document.querySelector('input[name="dispatch_out_time"]');
            if (dispatchOutTime?.value && !/^\d{2}:\d{2}$/.test(dispatchOutTime.value)) {
                isValid = false;
                showError(dispatchOutTime, 'Dispatch out time must be in HH:MM format.');
            } else {
                clearError(dispatchOutTime);
            }
            const paymentSlip = document.querySelector('input[name="payment_slip"]');
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            if (paymentSlip?.files?.length > 0) {
                const file = paymentSlip.files[0];
                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    showError(paymentSlip, 'Payment slip must be a JPG, PNG, PDF, DOC, DOCX, XLS, or XLSX file.');
                } else if (file.size > maxSize) {
                    isValid = false;
                    showError(paymentSlip, 'Payment slip must not exceed 2MB.');
                } else {
                    clearError(paymentSlip);
                }
            } else {
                clearError(paymentSlip);
            }
            const dispatchRemarks = document.querySelector('textarea[name="dispatch_remarks"]');
            if (dispatchRemarks?.value && dispatchRemarks.value.length > 2000) {
                isValid = false;
                showError(dispatchRemarks, 'Dispatch remarks must not exceed 2000 characters.');
            } else {
                clearError(dispatchRemarks);
            }
            const vehicleNo = document.querySelector('input[name="vehicle_no"]');
            if (vehicleNo?.value && (!/^[A-Z0-9 -]{5,20}$/.test(vehicleNo.value) || vehicleNo.value.length > 20)) {
                isValid = false;
                showError(vehicleNo, 'Vehicle number must be 5-20 alphanumeric characters with spaces or hyphens.');
            } else {
                clearError(vehicleNo);
            }
            const driverName = document.querySelector('input[name="driver_name"]');
            if (driverName?.value && driverName.value.length > 255) {
                isValid = false;
                showError(driverName, 'Driver name must not exceed 255 characters.');
            } else {
                clearError(driverName);
            }
            const driverMobile = document.querySelector('input[name="driver_mobile_no"]');
            if (driverMobile?.value && (!/^\d{10,15}$/.test(driverMobile.value) || driverMobile.value.length > 15)) {
                isValid = false;
                showError(driverMobile, 'Driver mobile must be 10-15 digits.');
            } else {
                clearError(driverMobile);
            }
            const eWayBillNo = document.querySelector('input[name="e_way_bill_no"]');
            if (eWayBillNo?.value && (!/^[A-Za-z0-9]+$/.test(eWayBillNo.value) || eWayBillNo.value.length > 50)) {
                isValid = false;
                showError(eWayBillNo, 'E-Way bill number must be alphanumeric and not exceed 50 characters.');
            } else {
                clearError(eWayBillNo);
            }
            const biltyNo = document.querySelector('input[name="bilty_no"]');
            if (biltyNo?.value && (!/^[A-Za-z0-9]+$/.test(biltyNo.value) || biltyNo.value.length > 50)) {
                isValid = false;
                showError(biltyNo, 'Bilty number must be alphanumeric and not exceed 50 characters.');
            } else {
                clearError(biltyNo);
            }
            const transportRemarks = document.querySelector('input[name="transport_remarks"]');
            if (transportRemarks?.value && transportRemarks.value.length > 2000) {
                isValid = false;
                showError(transportRemarks, 'Transport remarks must not exceed 2000 characters.');
            } else {
                clearError(transportRemarks);
            }
            const termsConditions = document.querySelector('textarea[name="terms_conditions"]');
            if (termsConditions?.value && termsConditions.value.length > 5000) {
                isValid = false;
                showError(termsConditions, 'Terms and conditions must not exceed 5000 characters.');
            } else {
                clearError(termsConditions);
            }
            const additionalCharges = document.querySelector('input[name="additional_charges"]');
            if (additionalCharges?.value && (isNaN(additionalCharges.value) || +additionalCharges.value < 0 || +additionalCharges.value > 99999999.99)) {
                isValid = false;
                showError(additionalCharges, 'Additional charges must be a number between 0 and 99,999,999.99.');
            } else {
                clearError(additionalCharges);
            }
            // 2. Items validation
            const itemRows = document.querySelectorAll('#item-list-container tr');
            if (itemRows.length === 0) {
                isValid = false;
                Swal.fire({ icon: 'error', title: 'Items Required', text: 'At least one item must be added.' });
            }
            const pairSet = new Set();
            const orderDispatchQuantities = {};
            itemRows.forEach((row, idx) => {
                const base = `items[${idx}]`;
                const get = (suffix) => row.querySelector(`[name="${base}${suffix}"]`);
                const orderId = get('[order_id]');
                const allocationId = get('[allocation_id]');
                const sizeId = get('[size]');
                const length = get('[length]');
                const dispatchQty = get('[dispatch_qty]');
                const basicPrice = get('[basic_price]');
                const gaugeDiff = get('[gauge_diff]');
                const finalPrice = get('[final_price]');
                const loading = get('[loading_charge]');
                const insurance = get('[insurance]');
                const gst = get('[gst]');
                const paymentTerm = get('[payment_term]');
                const tokenAmount = get('[token_amount]');
                const remark = get('[remark]');
                // Order ID
                if (!orderId?.value || isNaN(orderId.value)) {
                    isValid = false;
                    showError(orderId, 'Order is required and must be valid.');
                } else {
                    clearError(orderId);
                }
                // Allocation ID
                if (!allocationId?.value || isNaN(allocationId.value)) {
                    isValid = false;
                    showError(allocationId, 'Allocation is required and must be valid.');
                } else {
                    clearError(allocationId);
                }
                // Size
                if (!sizeId?.value || isNaN(sizeId.value)) {
                    isValid = false;
                    showError(sizeId, 'Size is required and must be valid.');
                } else {
                    clearError(sizeId);
                }
                // Duplicate allocation+size check
                if (allocationId?.value && sizeId?.value) {
                    const pair = `${allocationId.value}-${sizeId.value}`;
                    if (pairSet.has(pair)) {
                        isValid = false;
                        showError(sizeId, 'Duplicate allocation-size pair in the list.');
                    } else {
                        pairSet.add(pair);
                    }
                }
                // // Length
                // if (!length?.value || isNaN(length.value) || +length.value < 0 || +length.value > 999.99) {
                // isValid = false;
                // showError(length, 'Length must be a number between 0 and 999.99.');
                // } else {
                // clearError(length);
                // }
                // Dispatch Qty
                if (!dispatchQty?.value || isNaN(dispatchQty.value) || +dispatchQty.value < 0.01) {
                    isValid = false;
                    showError(dispatchQty, 'Dispatch quantity must be at least 0.01.');
                } else {
                    clearError(dispatchQty);
                    if (orderId?.value) {
                        orderDispatchQuantities[orderId.value] = (orderDispatchQuantities[orderId.value] || 0) + (+dispatchQty.value || 0);
                    }
                }
                // Numeric checks
                const numericCheck = (input, label, min = null, max = null) => {
                    if (!input?.value || isNaN(input.value)) {
                        isValid = false;
                        showError(input, `${label} must be a number.`);
                        return false;
                    }
                    const val = +input.value;
                    if ((min !== null && val < min) || (max !== null && val > max)) {
                        isValid = false;
                        showError(input, `${label} must be between ${min} and ${max}.`);
                        return false;
                    }
                    clearError(input);
                    return true;
                };
                numericCheck(basicPrice, 'Basic price', 0, 999999.99);
                numericCheck(gaugeDiff, 'Gauge difference', -999999.99, 999999.99);
                numericCheck(finalPrice, 'Final price', 0, 999999.99);
                numericCheck(loading, 'Loading charge', 0, 999999.99);
                numericCheck(insurance, 'Insurance charge', 0, 999999.99);
                numericCheck(gst, 'GST %', 0, 100);
                // Payment term
                const allowedTerms = ['Advance', 'Next Day', '15 Days Later', '30 Days Later'];
                if (!paymentTerm?.value || !allowedTerms.includes(paymentTerm.value)) {
                    isValid = false;
                    showError(paymentTerm, 'Select a valid payment term.');
                } else {
                    clearError(paymentTerm);
                }
                // Token amount
                if (tokenAmount?.value && tokenAmount.value !== 'N/A' && (isNaN(tokenAmount.value) || +tokenAmount.value < 0)) {
                    isValid = false;
                    showError(tokenAmount, 'Token amount must be a positive number or N/A.');
                } else {
                    clearError(tokenAmount);
                }
                // Remark
                if (remark?.value && remark.value.length > 2000) {
                    isValid = false;
                    showError(remark, 'Remark must not exceed 2000 characters.');
                } else {
                    clearError(remark);
                }
            });
            // 3. Dispatch quantity per order validation
            itemRows.forEach((row) => {
                const orderId = row.querySelector('[name*="[order_id]"]').value;
                const dispatchQty = parseFloat(row.querySelector('[name*="[dispatch_qty]"]').value) || 0;
                const remainingQty = parseFloat(row.querySelector('[name*="[remaining_qty]"]').value) || 0;
                if (orderId && dispatchQty > remainingQty + 5) {
                    isValid = false;
                    showError(row.querySelector('[name*="[dispatch_qty]"]'), `Dispatch quantity exceeds remaining quantity + 5 MT (Max: ${(remainingQty + 5).toFixed(2)} MT).`);
                }
            });
            // 4. Attachments validation
            const attachmentInputs = document.querySelectorAll('input[name^="attachments["][name$="][document]"]');
            attachmentInputs.forEach((inp, idx) => {
                if (inp.files?.length > 0) {
                    const file = inp.files[0];
                    if (!allowedTypes.includes(file.type)) {
                        isValid = false;
                        showError(inp, 'Attachment must be a JPG, PNG, PDF, DOC, DOCX, XLS, or XLSX file.');
                    } else if (file.size > maxSize) {
                        isValid = false;
                        showError(inp, 'Attachment must not exceed 2MB.');
                    } else {
                        clearError(inp);
                    }
                } else {
                    clearError(inp);
                }
                const remark = document.querySelector(`input[name="attachments[${idx}][remark]"]`);
                if (remark?.value && remark.value.length > 2000) {
                    isValid = false;
                    showError(remark, 'Attachment remark must not exceed 2000 characters.');
                } else {
                    clearError(remark);
                }
            });
            // 5. Clear errors on input/change
            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.addEventListener('input', () => clearError(el));
                el.addEventListener('change', () => clearError(el));
            });
            // 6. Handle submission
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please correct the highlighted fields before submitting.'
                });
                return;
            }
            // Submit the form if valid
            form.submit();
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function validatePincode(input, errorId) {
            const errorElement = document.getElementById(errorId);
            const pincode = input.value;
            const pincodeRegex = /^[0-9]{5,6}$/;
            if (!pincodeRegex.test(pincode)) {
                input.classList.add('is-invalid');
                errorElement.style.display = 'block';
                errorElement.textContent = 'Please enter a valid 5-6 digit PIN code.';
                return false;
            } else {
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
                errorElement.textContent = '';
                return true;
            }
        }
        let isBillNumberValid = true;
        function validateBillNumber(billNumber, input, errorElement) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            if (!billNumber) {
                input.classList.add('is-invalid');
                errorElement.style.display = 'block';
                errorElement.textContent = 'Bill number is required.';
                isBillNumberValid = false;
                return Promise.resolve(false);
            }
            return fetch(`/check-bill-number?bill_number=${encodeURIComponent(billNumber)}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.exists) {
                        input.classList.add('is-invalid');
                        errorElement.style.display = 'block';
                        errorElement.textContent = 'This bill number is already in use.';
                        isBillNumberValid = false;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate Bill Number',
                            text: 'This bill number is already in use. Please enter a unique bill number.',
                        });
                        return false;
                    } else {
                        input.classList.remove('is-invalid');
                        errorElement.style.display = 'none';
                        errorElement.textContent = '';
                        isBillNumberValid = true;
                        return true;
                    }
                })
                .catch(err => {
                    console.error('Error checking bill number:', err);
                    input.classList.add('is-invalid');
                    errorElement.style.display = 'block';
                    errorElement.textContent = 'Error checking bill number.';
                    isBillNumberValid = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to validate bill number: ' + err.message,
                    });
                    return false;
                });
        }
        const billNumberInput = document.getElementById('bill-number');
        const billNumberError = document.getElementById('bill-number-error');
        billNumberInput.addEventListener('input', function () {
            const billNumber = this.value.trim();
            validateBillNumber(billNumber, this, billNumberError);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('dispatch-form');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        // Helper: Show error
        function showError(input, message) {
            input.classList.add('is-invalid');
            const feedback = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            if (!input.parentNode.querySelector('.invalid-feedback')) {
                input.parentNode.appendChild(feedback);
            }
        }
        // Helper: Clear error
        function clearError(input) {
            input.classList.remove('is-invalid');
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        }
        // Helper: Fetch total remaining quantity for an order with unique allocations
        async function getOrderTotalRemainingQty(orderId) {
            if (!orderId) return 0;
            try {
                const response = await fetch(`/orders/${orderId}/allocations?remaining=true`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const allocations = await response.json();
                // Sum remaining quantities for unique allocation IDs
                const uniqueAllocations = new Map();
                allocations.forEach(alloc => {
                    if (!uniqueAllocations.has(alloc.id)) {
                        uniqueAllocations.set(alloc.id, parseFloat(alloc.remaining_qty) || 0);
                    }
                });
                const totalRemaining = Array.from(uniqueAllocations.values()).reduce((sum, qty) => sum + qty, 0);
                return totalRemaining;
            } catch (err) {
                console.error(`Error fetching allocations for order ${orderId}:`, err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Failed to fetch allocation data: ${err.message}`,
                });
                return 0;
            }
        }
        // Helper: Validate total dispatch quantity for an order
        async function validateOrderDispatchQty(orderId, dispatchQtyInput, row) {
            if (!orderId) return true;
            const totalRemaining = await getOrderTotalRemainingQty(orderId);
            const maxAllowed = totalRemaining + 5; // Threshold of 5 MT
            let totalDispatch = 0;
            // Sum dispatch quantities for the order across all rows
            document.querySelectorAll('#item-list-container tr').forEach(tr => {
                const rowOrderId = tr.querySelector('.order-select').value;
                if (rowOrderId === orderId) {
                    const qty = parseFloat(tr.querySelector('.dispatch-qty').value) || 0;
                    totalDispatch += qty;
                }
            });
            if (totalDispatch > maxAllowed) {
                showError(dispatchQtyInput, `Total dispatch quantity for this order cannot exceed ${maxAllowed.toFixed(2)} MT (remaining: ${totalRemaining.toFixed(2)} MT + 5 MT threshold).`);
                return false;
            }
            clearError(dispatchQtyInput);
            return true;
        }
        // Existing item calculation and grand total functions (unchanged)
        function calculateRowTotal(row) {
            let qty = parseFloat(row.find('input[name*="[dispatch_qty]"]').val()) || 0;
            let basicPrice = parseFloat(row.find('input[name*="[basic_price]"]').val()) || 0;
            let gaugeDiff = parseFloat(row.find('input[name*="[gauge_diff]"]').val()) || 0;
            let loading = parseFloat(row.find('input[name*="[loading_charge]"]').val()) || 0;
            let insurance = parseFloat(row.find('input[name*="[insurance]"]').val()) || 0;
            let gst = parseFloat(row.find('input[name*="[gst]"]').val()) || 0;
            let tokenAmount = row.find('input[name*="[token_amount]"]').val();
            tokenAmount = tokenAmount === 'N/A' ? 0 : parseFloat(tokenAmount) || 0;
            let finalPrice = basicPrice + gaugeDiff;
            row.find('input[name*="[final_price]"]').val(finalPrice.toFixed(2));
            let baseTotal = (finalPrice + loading + insurance) * qty;
            let gstAmt = baseTotal * (gst / 100);
            let total = baseTotal + gstAmt - tokenAmount;
            row.find('input[name*="[total_amount]"]').val(total.toFixed(2));
        }
        function updateGrandTotal() {
            let total = 0;
            $('.item-total').each((i, el) => total += parseFloat($(el).val()) || 0);
            let addCharges = parseFloat($('#additional-charges').val()) || 0;
            total += addCharges;
            $('#grand-total').text("₹" + total.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
            $('#total-amount-hidden').val(total.toFixed(2));
        }
        // Modified dispatch quantity input handler
        $('#item-list-container').on('input', 'input[name*="[dispatch_qty]"]', async function () {
            const row = $(this).closest('tr');
            const orderId = row.find('.order-select').val();
            const dispatchQty = parseFloat(this.value) || 0;
            const remainingQty = parseFloat(row.find('.remaining-qty').val()) || 0;
            if (orderId && dispatchQty > 0) {
                const valid = await validateOrderDispatchQty(orderId, this, row);
                if (!valid) {
                    this.value = '';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: `Total dispatch quantity for this order cannot exceed the sum of remaining quantities + 5 MT (Max: ${(remainingQty + 5).toFixed(2)} MT).`,
                    });
                } else if (dispatchQty > remainingQty + 5) {
                    this.value = '';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: `Dispatch quantity exceeds remaining quantity + 5 MT (Max: ${(remainingQty + 5).toFixed(2)} MT).`,
                    });
                }
            }
            calculateRowTotal(row);
            updateGrandTotal();
        });
    });
</script>
<script>
    $(document).ready(function () {
        const availableOrders = @json($orders);
        let selectedOrderIds = new Set();
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        function isDuplicatePair(row) {
            const currentAllocation = row.find('.allocation-select').val();
            const currentSize = row.find('.size-select').val();
            if (!currentAllocation || !currentSize) return false;
            let isDup = false;
            $('#item-list-container tr').each(function () {
                if (this === row[0]) return;
                const alloc = $(this).find('.allocation-select').val();
                const size = $(this).find('.size-select').val();
                if (alloc === currentAllocation && size === currentSize) {
                    isDup = true;
                    return false;
                }
            });
            return isDup;
        }
        $('#item-list-container').on('change', '.size-select', function () {
            const $select = $(this);
            const $row = $select.closest('tr');
            if (isDuplicatePair($row)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate',
                    text: 'Duplicate allocation and size pair.',
                });
                $select.val('');
            } else {
                const selectedOption = $select.find('option:selected');
                const rate = selectedOption.data('rate') || 0;
                $row.find('input[name*="[gauge_diff]"]').val(rate.toFixed(2)).trigger('change');
            }
        });
        function updateSerialNumbers() {
            $('#item-list-container tr').each((i, el) => $(el).find('.sn').text(i + 1));
        }
        function updateRemoveButtons() {
            let rows = $('#item-list-container tr');
            rows.find('.remove-item-row').show();
            if (rows.length === 1) rows.find('.remove-item-row').hide();
        }
        function refreshOrderDropdowns() {
            $('.order-select').each(function () {
                let currentVal = $(this).val();
                let $select = $(this);
                $select.find('option').each(function () {
                    let optionVal = $(this).val();
                    if (!optionVal) return;
                    if (selectedOrderIds.has(optionVal) && optionVal !== currentVal) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });
        }
        function calculateRowTotal(row) {
            let qty = parseFloat(row.find('input[name*="[dispatch_qty]"]').val()) || 0;
            let basicPrice = parseFloat(row.find('input[name*="[basic_price]"]').val()) || 0;
            let gaugeDiff = parseFloat(row.find('input[name*="[gauge_diff]"]').val()) || 0;
            let loading = parseFloat(row.find('input[name*="[loading_charge]"]').val()) || 0;
            let insurance = parseFloat(row.find('input[name*="[insurance]"]').val()) || 0;
            let gst = parseFloat(row.find('input[name*="[gst]"]').val()) || 0;
            let tokenAmount = row.find('input[name*="[token_amount]"]').val();
            tokenAmount = tokenAmount === 'N/A' ? 0 : parseFloat(tokenAmount) || 0;
            let finalPrice = basicPrice + gaugeDiff;
            row.find('input[name*="[final_price]"]').val(finalPrice.toFixed(2));
            let baseTotal = (finalPrice + loading + insurance) * qty;
            let gstAmt = baseTotal * (gst / 100);
            let total = baseTotal + gstAmt - tokenAmount;
            row.find('input[name*="[total_amount]"]').val(total.toFixed(2));
        }
        function updateGrandTotal() {
            let total = 0;
            $('.item-total').each((i, el) => total += parseFloat($(el).val()) || 0);
            let addCharges = parseFloat($('#additional-charges').val()) || 0;
            total += addCharges;
            $('#grand-total').text("₹" + total.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
            $('#total-amount-hidden').val(total.toFixed(2));
        }
        $('#item-list-container').on('change', '.order-select', function () {
            let $select = $(this);
            let row = $select.closest('tr');
            let newId = $select.val();
            let prevId = $select.data('previous');
            if (prevId) selectedOrderIds.delete(prevId);
            if (newId) selectedOrderIds.add(newId);
            $select.data('previous', newId);
            fetch(`/orders/${newId}/allocations?remaining=true`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    let allocationSelect = row.find('.allocation-select');
                    allocationSelect.html('<option value="">Select Allocation</option>');
                    let index = 1; // Counter for serial numbers
                    data.forEach(allocation => {
                        let tokenAmount = allocation.token_amount;
                        tokenAmount = (tokenAmount != null && !isNaN(parseFloat(tokenAmount))) ? parseFloat(tokenAmount).toFixed(2) : 'N/A';
                        let entityName = allocation.type === 'dealer' ? (allocation.dealer_name || 'N/A') : (allocation.distributor_name || 'N/A');
                        let entityCode = allocation.type === 'dealer' ? (allocation.dealer_code || 'N/A') : (allocation.distributor_code || 'N/A');
                        let optionText = `${index}) (For ${allocation.type}) : ${entityName} (${entityCode}) - Remaining: ${allocation.remaining_qty} MT`;
                        allocationSelect.append(`<option value="${allocation.id}" data-remaining="${allocation.remaining_qty}" data-payment-term="${allocation.payment_terms || ''}" data-token-amount="${tokenAmount}">${optionText}</option>`);
                        index++; // Increment the counter for the next option
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `Failed to fetch allocation list: ${err.message}`,
                    });
                });
            calculateRowTotal(row);
            updateGrandTotal();
        });
        $('#item-list-container').on('change', '.allocation-select', function () {
            let row = $(this).closest('tr');
            let $select = $(this);
            let allocationId = $select.val();
            let selectedOption = $select.find('option:selected');
            let remainingQty = parseFloat(selectedOption.data('remaining')) || 0;
            let paymentTerm = selectedOption.data('payment-term') || '';
            let tokenAmount = selectedOption.data('token-amount') || 'N/A';
            if (isDuplicatePair(row)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate',
                    text: 'Duplicate allocation and size pair.',
                });
                $select.val('');
                row.find('.basic-price-input').val('');
                row.find('.token-amount').val('N/A');
                return;
            }
            if (allocationId) {
                row.find('.basic-price-input').val('Loading...');
                row.find('.token-amount').val(tokenAmount);
                fetch(`/api/allocations/${allocationId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        row.find('input[name*="[already_disp]"]').val(data.dispatched_qty || 0);
                        row.find('input[name*="[remaining_qty]"]').val(remainingQty);
                        const basicPrice = data.agreed_basic_price !== null && !isNaN(data.agreed_basic_price) ? data.agreed_basic_price : 0;
                        row.find('.basic-price-input').val(basicPrice.toFixed(2));
                        row.find('select[name*="[payment_term]"]').val(data.payment_term || '').trigger('change');
                        row.find('input[name*="[loading_charge]"]').val(data.loading_charge || {{ $loadingCharge ?? 265 }});
                        row.find('input[name*="[insurance]"]').val(data.insurance_charge || {{ $insuranceCharge ?? 40 }});
                        row.find('input[name*="[gst]"]').val(data.gst_rate || {{ $gstRate ?? 18 }});
                        let tokenAmount = data.token_amount;
                        tokenAmount = (tokenAmount != null && !isNaN(parseFloat(tokenAmount))) ? parseFloat(tokenAmount).toFixed(2) : 'N/A';
                        row.find('input[name*="[token_amount]"]').val(tokenAmount);
                        let already = parseFloat(row.find('input[name*="[already_disp]"]').val()) || 0;
                        let remaining = parseFloat(row.find('input[name*="[remaining_qty]"]').val()) || 0;
                        row.find('input[name*="[order_qty]"]').val((already + remaining).toFixed(2));
                        calculateRowTotal(row);
                        updateGrandTotal();
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `Failed to fetch allocation details: ${err.message}`,
                        });
                        row.find('.basic-price-input').val('0.00');
                        row.find('input[name*="[already_disp]"]').val('0');
                        row.find('input[name*="[remaining_qty]"]').val(remainingQty.toFixed(2));
                        row.find('input[name*="[order_qty]"]').val(remainingQty.toFixed(2));
                        row.find('input[name*="[token_amount]"]').val('N/A');
                        calculateRowTotal(row);
                        updateGrandTotal();
                    });
            } else {
                row.find('.basic-price-input').val('');
                row.find('input[name*="[already_disp]"]').val('');
                row.find('input[name*="[remaining_qty]"]').val('');
                row.find('input[name*="[order_qty]"]').val('');
                row.find('select[name*="[payment_term]"]').val('').trigger('change');
                row.find('input[name*="[token_amount]"]').val('N/A');
                calculateRowTotal(row);
                updateGrandTotal();
            }
        });
        $('#item-list-container').on('keyup change', '.item-calc', function () {
            const row = $(this).closest('tr');
            calculateRowTotal(row);
            updateGrandTotal();
        });
        $('#additional-charges').on('keyup change', function () {
            updateGrandTotal();
        });
        $('#add-item-row').click(function () {
            let lastRow = $('#item-list-container tr:last');
            let newRow = lastRow.clone();
            let newIndex = $('#item-list-container tr').length;
            newRow.find('input, select').each(function () {
                let name = $(this).attr('name');
                if (name) {
                    let updatedName = name.replace(/\[\d+\]/, `[${newIndex}]`);
                    $(this).attr('name', updatedName);
                }
                $(this).val('');
            });
            newRow.find('input[readonly]').val('Auto Fetch');
            newRow.find('.item-total').val('').attr('placeholder', 'Auto Calc');
            newRow.find('input[name*="[final_price]"]').val('').attr('placeholder', 'Auto Calc');
            newRow.find('input[name*="[token_amount]"]').val('N/A');
            newRow.find('.order-select').data('previous', '');
            newRow.find('.allocation-select').html('<option value="">Select Allocation</option>');
            newRow.find('input[name*="[item_name]"]').val('{{ $singleItemName ?? 'TMT Bar' }}');
            // newRow.find('input[name*="[length]"]').val('12');
            newRow.find('input[name*="[loading_charge]"]').val({{ $loadingCharge ?? 265 }});
            newRow.find('input[name*="[insurance]"]').val({{ $insuranceCharge ?? 40 }});
            newRow.find('input[name*="[gst]"]').val({{ $gstRate ?? 18 }});
            newRow.find('.basic-price-input').val('');
            $('#item-list-container').append(newRow);
            updateSerialNumbers();
            updateRemoveButtons();
        });
        $('#item-list-container').on('click', '.remove-item-row', function () {
            let row = $(this).closest('tr');
            let orderId = row.find('.order-select').val();
            if (orderId) selectedOrderIds.delete(orderId);
            row.remove();
            updateSerialNumbers();
            updateRemoveButtons();
            updateGrandTotal();
        });
        $('#item-list-container').on('change', 'select[name*="[payment_term]"]', function () {
            let $this = $(this);
            let value = $this.val();
            let name = $this.attr('name');
            let $hidden = $this.siblings('input[type="hidden"][name="' + name + '"]');
            if (value) {
                $this.prop('disabled', true);
                if ($hidden.length) {
                    $hidden.val(value);
                } else {
                    $this.after('<input type="hidden" name="' + name + '" value="' + value + '">');
                }
            } else {
                $this.prop('disabled', false);
                $hidden.remove();
            }
        });
        $('#item-list-container').on('input', 'input[name*="[dispatch_qty]"]', async function () {
            const row = $(this).closest('tr');
            const orderId = row.find('.order-select').val();
            const dispatchQty = parseFloat(this.value) || 0;
            const remainingQty = parseFloat(row.find('.remaining-qty').val()) || 0;
            if (orderId) {
                const valid = await validateOrderDispatchQty(orderId, this, row);
                if (!valid) {
                    this.value = '';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: `Total dispatch quantity for this order cannot exceed the sum of remaining quantities + 5 MT.`,
                    });
                } else if (dispatchQty > remainingQty + 5) {
                    this.value = '';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: `Dispatch quantity exceeds remaining quantity + 5 MT (Max: ${(remainingQty + 5).toFixed(2)} MT).`,
                    });
                }
            }
            calculateRowTotal(row);
            updateGrandTotal();
        });
        $('#add-attachment-row').click(function () {
            let lastRow = $('#attachment-container tbody tr:last');
            let newIndex = $('#attachment-container tbody tr').length;
            let newRow = lastRow.clone();
            newRow.find('input').each(function () {
                let name = $(this).attr('name');
                if (name) {
                    let updatedName = name.replace(/\[\d+\]/, `[${newIndex}]`);
                    $(this).attr('name', updatedName);
                }
                $(this).val('');
            });
            newRow.find('.file-label').text('Choose file');
            newRow.find('.file-name').text('');
            newRow.find('input[type="file"]').attr('id', `attachment_${newIndex}`);
            newRow.find('.custom-file-upload').attr('for', `attachment_${newIndex}`);
            newRow.addClass('new-row-animation');
            $('#attachment-container tbody').append(newRow);
            updateAttachmentRemoveButtons();
        });
        $('#attachment-container').on('click', '.remove-attachment-row', function () {
            let row = $(this).closest('tr');
            row.remove();
            updateAttachmentRemoveButtons();
        });
        function updateAttachmentRemoveButtons() {
            let rows = $('#attachment-container tbody tr');
            rows.find('.remove-attachment-row').show();
            if (rows.length === 1) rows.find('.remove-attachment-row').hide();
        }
        $(document).on('change', '.attachment-input', function () {
            const input = this;
            const $inputGroup = $(this).closest('.input-group');
            const $label = $inputGroup.find('.file-label');
            const $fileName = $inputGroup.find('.file-name');
            const $customUpload = $inputGroup.find('.custom-file-upload');
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            const maxSize = 2 * 1024 * 1024;
            if (input.files.length > 0) {
                const file = input.files[0];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type',
                        text: 'Allowed file types: jpg, jpeg, png, pdf, doc, docx, xls, xlsx.',
                    });
                    input.value = '';
                    $label.text('Choose file');
                    $fileName.text('');
                    $customUpload.removeClass('file-selected');
                    return;
                }
                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'File size must not exceed 2MB.',
                    });
                    input.value = '';
                    $label.text('Choose file');
                    $fileName.text('');
                    $customUpload.removeClass('file-selected');
                    return;
                }
                $label.text('File selected:');
                $fileName.text(file.name);
                $customUpload.addClass('file-selected');
            } else {
                $label.text('Choose file');
                $fileName.text('');
                $customUpload.removeClass('file-selected');
            }
        });
    })
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        function loadCities(stateSelector, citySelector, selectedCityId) {
            let stateId = $(stateSelector).val();
            let $cityDropdown = $(citySelector);
            console.log(`loadCities called: stateSelector=${stateSelector}, stateId=${stateId}, citySelector=${citySelector}, selectedCityId=${selectedCityId}`);
            if (!stateId) {
                $cityDropdown.html('<option value="">-- Select City --</option>');
                console.warn(`No state selected for ${stateSelector}`);
                return;
            }
            $cityDropdown.html('<option value="">Loading...</option>');
            $.ajax({
                url: `/get-cities/${stateId}`,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (data) {
                    console.log(`Cities received for state ${stateId}:`, data);
                    $cityDropdown.empty().append('<option value="">-- Select City --</option>');
                    let foundSelected = false;
                    data.forEach(function (city) {
                        let selected = city.id.toString() === selectedCityId?.toString() ? 'selected' : '';
                        if (selected) foundSelected = true;
                        console.log(`City: ${city.name}, ID: ${city.id}, Selected: ${selected}`);
                        $cityDropdown.append(`<option value="${city.id}" ${selected}>${city.name}</option>`);
                    });
                    // if (selectedCityId && !foundSelected) {
                    // console.warn(`City ID ${selectedCityId} not found in response for state ${stateId}`);
                    // Swal.fire({
                    // icon: 'warning',
                    // title: 'City Not Found',
                    // text: `The city with ID ${selectedCityId} was not found for the selected state.`,
                    // });
                    // }
                },
                error: function (err) {
                    console.error(`Error fetching cities for state ${stateId}:`, err);
                    $cityDropdown.html('<option value="">Error loading cities</option>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load cities. Please try again.',
                    });
                }
            });
        }
        $('#state').on('change', function () {
            console.log('State 1 changed to:', $(this).val());
            loadCities('#state', '#city', '{{ old('recipient_city', $party->city_id ?? '') }}');
        });
        $('#state2').on('change', function () {
            console.log('State 2 changed to:', $(this).val());
            loadCities('#state2', '#city2', '{{ old('consignee_city', $party->city_id ?? '') }}');
        });
        // Trigger initial city load
        console.log('Initial State 1 value:', $('#state').val());
        console.log('Initial State 2 value:', $('#state2').val());
        console.log('Selected City ID 1:', '{{ old('recipient_city', $party->city_id ?? '') }}');
        console.log('Selected City ID 2:', '{{ old('consignee_city', $party->city_id ?? '') }}');
        $('#state').trigger('change');
        $('#state2').trigger('change');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const dispatchDateInput = document.getElementById('dispatch_date');
        const dispatchNumberInput = document.getElementById('dispatch_number');
        if (!dispatchDateInput || !dispatchNumberInput) {
            console.error('Dispatch date or number input not found');
            return;
        }
        console.log('Initial dispatch_date value:', dispatchDateInput.value);
        function fetchDispatchNumber(date) {
            console.log('Fetching dispatch number for date:', date);
            fetch(`/generate-dispatch-number?date=${date}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(res => {
                    console.log('Fetch response status:', res.status);
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    console.log('Dispatch Number Data:', data);
                    if (data.dispatch_number) {
                        dispatchNumberInput.value = data.dispatch_number;
                    } else {
                        dispatchNumberInput.value = 'Error: No dispatch number';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No dispatch number received from server.',
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching dispatch number:', err);
                    dispatchNumberInput.value = 'Error: Unable to generate';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to generate dispatch number: ' + err.message,
                    });
                });
        }
        dispatchDateInput.addEventListener('change', function () {
            const selectedDate = this.value;
            console.log('Dispatch date changed to:', selectedDate);
            if (selectedDate) {
                fetchDispatchNumber(selectedDate);
            } else {
                dispatchNumberInput.value = 'Auto-Generated';
            }
        });
        if (dispatchDateInput.value) {
            console.log('Triggering initial dispatch number fetch');
            fetchDispatchNumber(dispatchDateInput.value);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const consigneeInput = document.getElementById('recipient-name');
        const billToInput = document.getElementById('bill-to');
        if (consigneeInput && billToInput) {
            billToInput.value = consigneeInput.value;
            consigneeInput.addEventListener('input', function () {
                billToInput.value = this.value;
            });
        }
    });
</script>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form         = document.getElementById('dispatch-form');
    const submitBtn    = form.querySelector('button[type="submit"]'); // the "Save Dispatch" button
    const originalHtml = submitBtn.innerHTML;                         // keep original content

    form.addEventListener('submit', function () {
        // 1. Disable the button immediately
        submitBtn.disabled = true;

        // 2. Replace button text with spinner + "Saving..."
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Saving...
        `;

        // 3. (Optional but recommended) Re-enable button if submission fails / page stays
        //    (e.g. server validation error, network issue, etc.)
        window.addEventListener('pageshow', function () {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        });

        // If you ever need to re-enable manually (e.g. AJAX fallback), just call:
        // submitBtn.disabled = false;
        // submitBtn.innerHTML = originalHtml;
    });
});
</script>
@endpush
