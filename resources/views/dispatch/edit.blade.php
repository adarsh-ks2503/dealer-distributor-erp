@extends('layouts.main')
@section('title', 'Singhal - Edit Dispatch')
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
                    <h1 class="h3 mb-0">Edit Dispatch - {{ $dispatch->type }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dispatch.index') }}">Dispatch</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('dispatch.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
        <form action="{{ route('dispatch.update', $dispatch->id) }}" method="POST" enctype="multipart/form-data" id="dispatch-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="{{ $dispatch->type }}">
            @if ($dispatch->type == 'distributor')
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
                                <div class="detail-item"><label>Order Type</label><span>{{ $dispatch->type }}</span></div>
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
                                    <input required id="recipient-name" name="recipient_name" type="text" class="form-control" placeholder="Recipient Name" value="{{ old('recipient_name', $dispatch->recipient_name) }}">
                                    <textarea required name="recipient_address" class="form-control" rows="2" placeholder="Billing Address">{{ old('recipient_address', $dispatch->recipient_address) }}</textarea>
                                    <select required name="recipient_state" id="state" class="form-select">
                                        <option value="">Select</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}" {{ old('recipient_state', $dispatch->recipient_state_id) == $state->id ? 'selected' : '' }}>{{ $state->state }}</option>
                                        @endforeach
                                    </select>
                                    <select required name="recipient_city" class="form-select" id="city">
                                        <option value="">Select City</option>
                                    </select>
                                    <div>
                                        <input required name="recipient_pincode" type="text" class="form-control" placeholder="Pincode" pattern="[0-9]{6}" maxlength="6" oninput="validatePincode(this, 'recipient-pincode-error')" value="{{ old('recipient_pincode', $dispatch->recipient_pincode) }}">
                                        <span id="recipient-pincode-error" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100"><div class="card-header"><i class="fas fa-truck me-2"></i>Delivery Address <span class="required-asterisk">*</span></div>
                                <div class="card-body d-flex flex-column gap-3 mt-2">
                                    <input required name="consignee_name" id="consignee-name" type="text" class="form-control" placeholder="Consignee Name" value="{{ old('consignee_name', $dispatch->consignee_name) }}">
                                    <textarea required name="consignee_address" class="form-control" rows="2" placeholder="Delivery Address">{{ old('consignee_address', $dispatch->consignee_address) }}</textarea>
                                    <select required name="consignee_state" class="form-select" id="state2">
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}" {{ old('consignee_state', $dispatch->consignee_state_id) == $state->id ? 'selected' : '' }}>{{ $state->state }}</option>
                                        @endforeach
                                    </select>
                                    <select required name="consignee_city" class="form-select" id="city2">
                                        <option value="">Select City</option>
                                    </select>
                                    <div>
                                        <input required name="consignee_pincode" type="text" class="form-control" placeholder="Pincode" pattern="[0-9]{6}" maxlength="6" oninput="validatePincode(this, 'consignee-pincode-error')" value="{{ old('consignee_pincode', $dispatch->consignee_pincode) }}">
                                        <span id="consignee-pincode-error" class="text-danger"></span>
                                    </div>
                                    <input required name="consignee_mobile_no" type="number" class="form-control" placeholder="Mobile Number" value="{{ old('consignee_mobile_no', $dispatch->consignee_mobile_no) }}">
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
                            <select required name="warehouse_id" class="form-select" id="warehouse-select">
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $dispatch->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dispatch Number</label>
                            <input id="dispatch_number" name="dispatch_number" type="text" class="form-control" readonly value="{{ old('dispatch_number', $dispatch->dispatch_number) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dealer/Distributor Name</label>
                            <input type="text" class="form-control" readonly value="{{ $party->name }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dispatch Date <span class="required-asterisk">*</span></label>
                            <input type="date" id="dispatch_date" name="dispatch_date" class="form-control" required value="{{ old('dispatch_date', $dispatch->dispatch_date instanceof \Carbon\Carbon ? $dispatch->dispatch_date->format('Y-m-d') : $dispatch->dispatch_date) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bill To</label>
                            <input name="bill_to" id="bill-to" type="text" class="form-control" placeholder="Consignee Name" value="{{ old('bill_to', $dispatch->bill_to) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bill No</label>
                            <input id="bill-number" name="bill_number" type="text" class="form-control" placeholder="Bill Number" value="{{ old('bill_number', $dispatch->bill_number) }}">
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
                                    value="{{ old('dispatch_out_time_display', $dispatch->dispatch_out_time ? \Carbon\Carbon::createFromFormat('H:i', $dispatch->dispatch_out_time)->format('h:i A') : '') }}"
                                    autocomplete="off"
                                >
                                <span class="input-group-text">
                                    <i class="fas fa-clock"></i>
                                </span>
                            </div>
                            <input type="hidden" name="dispatch_out_time" id="dispatch_out_time_hidden" value="{{ old('dispatch_out_time', $dispatch->dispatch_out_time) }}">
                            <span id="dispatch-out-time-error" class="text-danger"></span>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Slip</label>
                            <div class="input-group">
                                <label for="paymentSlip" class="custom-file-upload w-100">
                                    <i class="fas fa-cloud-upload-alt me-1"></i>
                                    <span class="file-label">{{ $dispatch->payment_slip ? 'Replace File' : 'Choose file' }}</span>
                                    <span class="file-name text-muted ms-2">{{ $dispatch->payment_slip ? basename($dispatch->payment_slip) : '' }}</span>
                                </label>
                                <input name="payment_slip" type="file" id="paymentSlip" class="attachment-input">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Remark</label>
                            <textarea name="dispatch_remarks" class="form-control" rows="2">{{ old('dispatch_remarks', $dispatch->dispatch_remarks) }}</textarea>
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
                            <input name="transporter_name" type="text" class="form-control" value="{{ old('transporter_name', $dispatch->transporter_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vehicle No.</label>
                            <input name="vehicle_no" type="text" class="form-control" value="{{ old('vehicle_no', $dispatch->vehicle_no) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Driver Name</label>
                            <input name="driver_name" type="text" class="form-control" value="{{ old('driver_name', $dispatch->driver_name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Driver Mobile</label>
                            <input name="driver_mobile_no" type="text" class="form-control" value="{{ old('driver_mobile_no', $dispatch->driver_mobile_no) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">E-Way Bill No.</label>
                            <input name="e_way_bill_no" type="text" class="form-control" value="{{ old('e_way_bill_no', $dispatch->e_way_bill_no) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bilty No.</label>
                            <input name="bilty_no" type="text" class="form-control" value="{{ old('bilty_no', $dispatch->bilty_no) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remark</label>
                            <input name="transport_remarks" type="text" class="form-control" value="{{ old('transport_remarks', $dispatch->transport_remarks) }}">
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
                                @php
                                    $items = old('items', $dispatch->items->toArray());
                                @endphp
                                @foreach ($items as $index => $item)
                                    <tr>
                                        <td><span class="sn">{{ $index + 1 }}</span></td>
                                        <td>
                                            <select required name="items[{{ $index }}][order_id]" class="form-select form-select-sm order-select">
                                                <option value="">Select</option>
                                                @foreach ($orders as $order)
                                                    <option value="{{ $order->id }}" {{ old('items.' . $index . '.order_id', $item['order_id'] ?? '') == $order->id ? 'selected' : '' }}>{{ $order->order_number }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select required name="items[{{ $index }}][allocation_id]" class="form-select form-select-sm allocation-select">
                                                <option value="">Select</option>
                                                @if (isset($item['order_id']) && $item['order_id'])
                                                    @php
                                                        $order = $orders->find($item['order_id']);
                                                        $allocations = $order ? $order->allocations : [];
                                                    @endphp
                                                    @foreach ($allocations as $allocation)
                                                        <option value="{{ $allocation->id }}" {{ old('items.' . $index . '.allocation_id', $item['allocation_id'] ?? '') == $allocation->id ? 'selected' : '' }}
                                                                data-remaining="{{ $allocation->remaining_qty }}"
                                                                data-payment-term="{{ $allocation->payment_terms }}"
                                                                data-token-amount="{{ $allocation->token_amount ?? 'N/A' }}">
                                                            {{ $loop->iteration }}- (For {{ $allocation->allocated_to_type }}) :
                                                            {{ $allocation->allocated_to_type === 'dealer' ? ($allocation->allocatable->name ?? 'N/A') : ($allocation->allocatable->name ?? 'N/A') }}
                                                            ({{ $allocation->allocated_to_type === 'dealer' ? ($allocation->allocatable->code ?? 'N/A') : ($allocation->allocatable->code ?? 'N/A') }}) -
                                                            Remaining: {{ $allocation->remaining_qty }} MT
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td><input type="text" name="items[{{ $index }}][order_qty]" class="form-control form-control-sm" readonly value="{{ old('items.' . $index . '.order_qty', $item['order_qty'] ?? '') }}"></td>
                                        <td><input type="text" name="items[{{ $index }}][already_disp]" class="form-control form-control-sm" readonly value="{{ old('items.' . $index . '.already_disp', $item['already_disp'] ?? '') }}"></td>
                                        <td><input type="text" name="items[{{ $index }}][remaining_qty]" class="form-control form-control-sm remaining-qty" readonly value="{{ old('items.' . $index . '.remaining_qty', $item['remaining_qty'] ?? '') }}"></td>
                                        <td><input type="text" name="items[{{ $index }}][item_name]" class="form-control form-control-sm" value="{{ old('items.' . $index . '.item_name', $item['item_name'] ?? ($singleItemName ?? 'TMT Bar')) }}" readonly></td>
                                        <td>
                                            <select required name="items[{{ $index }}][size]" class="form-select form-select-sm size-select">
                                                <option value="">Select</option>
                                                @foreach ($sizes as $size)
                                                    <option data-rate="{{ $size->rate }}" value="{{ $size->id }}" {{ old('items.' . $index . '.size', $item['size_id'] ?? '') == $size->id ? 'selected' : '' }}>{{ $size->size }} mm</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input required type="number" step="0.01" name="items[{{ $index }}][dispatch_qty]" class="form-control form-select-sm dispatch-qty item-calc" placeholder="Enter" value="{{ old('items.' . $index . '.dispatch_qty', $item['dispatch_qty'] ?? '') }}"></td>
                                        <td><input readonly type="number" step="0.01" name="items[{{ $index }}][basic_price]" class="form-control form-control-sm item-calc basic-price-input" value="{{ old('items.' . $index . '.basic_price', $item['basic_price'] ?? '') }}"></td>
                                        <td><input type="number" step="0.01" name="items[{{ $index }}][gauge_diff]" class="form-control form-control-sm item-calc" readonly value="{{ old('items.' . $index . '.gauge_diff', $item['gauge_diff'] ?? '') }}"></td>
                                        <td><input type="text" name="items[{{ $index }}][final_price]" class="form-control form-control-sm" placeholder="Auto Calc" readonly value="{{ old('items.' . $index . '.final_price', $item['final_price'] ?? '') }}"></td>
                                        <td><input type="number" step="0.01" name="items[{{ $index }}][loading_charge]" class="form-control form-control-sm item-calc" value="{{ old('items.' . $index . '.loading_charge', $item['loading_charge'] ?? ($loadingCharge ?? 265)) }}" readonly></td>
                                        <td><input type="number" step="0.01" name="items[{{ $index }}][insurance]" class="form-control form-control-sm item-calc" value="{{ old('items.' . $index . '.insurance', $item['insurance'] ?? ($insuranceCharge ?? 40)) }}" readonly></td>
                                        <td><input type="number" step="0.01" name="items[{{ $index }}][gst]" class="form-control form-control-sm item-calc" value="{{ old('items.' . $index . '.gst', $item['gst'] ?? ($gstRate ?? 18)) }}" readonly></td>
                                        <td><input type="text" name="items[{{ $index }}][token_amount]" class="form-control form-control-sm token-amount" readonly value="{{ old('items.' . $index . '.token_amount', $item['token_amount'] ?? '0.00') }}"></td>
                                        <td><input type="text" name="items[{{ $index }}][total_amount]" class="form-control form-control-sm item-total" placeholder="Auto Calc" readonly value="{{ old('items.' . $index . '.total_amount', $item['total_amount'] ?? '') }}"></td>
                                        <td>
                                            <select name="items[{{ $index }}][payment_term]" class="form-select form-select-sm payment-term-select" disabled>
                                                <option value="">Select</option>
                                                <option value="Advance" {{ old('items.' . $index . '.payment_term', $item['payment_term'] ?? '') == 'Advance' ? 'selected' : '' }}>Advance</option>
                                                <option value="Next Day" {{ old('items.' . $index . '.payment_term', $item['payment_term'] ?? '') == 'Next Day' ? 'selected' : '' }}>Next Day</option>
                                                <option value="15 Days Later" {{ old('items.' . $index . '.payment_term', $item['payment_term'] ?? '') == '15 Days Later' ? 'selected' : '' }}>15 Days Later</option>
                                                <option value="30 Days Later" {{ old('items.' . $index . '.payment_term', $item['payment_term'] ?? '') == '30 Days Later' ? 'selected' : '' }}>30 Days Later</option>
                                            </select>
                                            @if (old('items.' . $index . '.payment_term', $item['payment_term'] ?? ''))
                                                <input type="hidden" name="items[{{ $index }}][payment_term]" value="{{ old('items.' . $index . '.payment_term', $item['payment_term'] ?? '') }}">
                                            @endif
                                        </td>
                                        <td><input type="text" name="items[{{ $index }}][remark]" class="form-control form-control-sm" placeholder="Remark" value="{{ old('items.' . $index . '.remark', $item['remark'] ?? '') }}"></td>
                                        <td class="action-cell"><button type="button" class="btn btn-sm btn-danger remove-item-row"><i class="fas fa-trash-alt"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                        <textarea name="terms_conditions" id="terms_conditions" class="summernote">{!! $dispatch->terms_conditions !!}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-paperclip me-2"></i>Attachments</span>
                            <button type="button" class="btn btn-sm btn-success" id="add-attachment-row"><i class="fas fa-plus"></i> Add Attachment</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="attachment-container">
                                    <thead>
                                        <tr>
                                            <th>Upload Document</th>
                                            <th>Remark</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attachment-table-body">
                                        @php
                                            $oldAttachments = old('attachments', []);
                                            $existingAttachments = $dispatch->attachments->toArray();
                                            $combinedAttachments = [];
                                            foreach ($existingAttachments as $index => $attachment) {
                                                $combinedAttachments[$index] = [
                                                    'id' => $attachment['id'] ?? null,
                                                    'document' => $attachment['document'] ?? null,
                                                    'remark' => $oldAttachments[$index]['remark'] ?? $attachment['remark'] ?? '',
                                                    'file_name' => $attachment['document'] ? basename($attachment['document']) : (isset($oldAttachments[$index]['file_name']) ? $oldAttachments[$index]['file_name'] : ''),
                                                ];
                                            }
                                            foreach ($oldAttachments as $index => $oldAttachment) {
                                                if (!isset($existingAttachments[$index]) && !isset($oldAttachment['id'])) {
                                                    $combinedAttachments[$index] = [
                                                        'id' => null,
                                                        'document' => null,
                                                        'remark' => $oldAttachment['remark'] ?? '',
                                                        'file_name' => $oldAttachment['file_name'] ?? '',
                                                    ];
                                                }
                                            }
                                            if (empty($combinedAttachments)) {
                                                $combinedAttachments[] = [
                                                    'id' => null,
                                                    'document' => null,
                                                    'remark' => '',
                                                    'file_name' => '',
                                                ];
                                            }
                                        @endphp
                                        @foreach ($combinedAttachments as $index => $attachment)
                                            <tr class="attachment-row" data-index="{{ $index }}">
                                                <td>
                                                    <div class="input-group">
                                                        <label for="attachment_{{ $index }}" class="custom-file-upload w-100 {{ $attachment['document'] || $attachment['file_name'] ? 'file-selected' : '' }}" data-original-file="{{ $attachment['file_name'] }}">
                                                            <i class="fas fa-cloud-upload-alt me-1"></i>
                                                            <span class="file-label">{{ ($attachment['document'] || $attachment['file_name']) ? 'Replace File' : 'Choose file' }}</span>
                                                            <span class="file-name text-muted ms-2">{{ $attachment['file_name'] }}</span>
                                                        </label>
                                                        <input type="file" name="attachments[{{ $index }}][document]" id="attachment_{{ $index }}" class="attachment-input" data-original-file="{{ $attachment['file_name'] }}">
                                                        @if ($attachment['id'])
                                                            <input type="hidden" name="attachments[{{ $index }}][id]" value="{{ $attachment['id'] }}">
                                                        @endif
                                                        <input type="hidden" name="attachments[{{ $index }}][file_name]" value="{{ $attachment['file_name'] }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="attachments[{{ $index }}][remark]" class="form-control" placeholder="Remark" value="{{ old('attachments.' . $index . '.remark', $attachment['remark']) }}">
                                                </td>
                                                <td class="action-cell">
                                                    <button type="button" class="btn btn-sm btn-danger remove-attachment-row"><i class="fas fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
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
                                <input type="number" name="additional_charges" id="additional-charges" class="form-control" placeholder="Enter amount" value="{{ old('additional_charges', $dispatch->additional_charges) }}"></div><hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Total Amount:</h5>
                                <h4 class="mb-0 text-success fw-bold" id="grand-total">₹{{ number_format(old('total_amount', $dispatch->total_amount), 2, '.', ',') }}</h4>
                                <input type="hidden" name="total_amount" id="total-amount-hidden" value="{{ old('total_amount', $dispatch->total_amount) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end my-4">
                <a href="{{ route('dispatch.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Dispatch</button>
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
</style>
@endpush
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.css" rel="stylesheet">
@endpush
@push('scripts')
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
    const initial24 = '{{ old('dispatch_out_time', $dispatch->dispatch_out_time) }}';
    if (initial24) {
        const [hours, minutes] = initial24.split(':').map(Number);
        const period = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        displayInput.value = `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${period}`;
        hiddenInput.value = initial24;
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function showError(input, message) {
            input.classList.add('is-invalid');
            let feedback = input.parentElement.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentElement.appendChild(feedback);
            }
            feedback.textContent = message;
        }

        function clearError(input) {
            input.classList.remove('is-invalid');
            const feedback = input.parentElement.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        }

        function validatePincode(input, errorId) {
            const errorElement = document.getElementById(errorId);
            const pincode = input.value;
            const pincodeRegex = /^[0-9]{6}$/;
            if (!pincodeRegex.test(pincode)) {
                input.classList.add('is-invalid');
                errorElement.style.display = 'block';
                errorElement.textContent = 'Please enter a valid 6-digit PIN code.';
                return false;
            } else {
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
                errorElement.textContent = '';
                return true;
            }
        }
        function validateTime(input) {
            const errorElement = document.getElementById('dispatch-out-time-error');
            const time = input.value;
            const timeRegex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
            if (time && !timeRegex.test(time)) {
                input.classList.add('is-invalid');
                errorElement.style.display = 'block';
                errorElement.textContent = 'Please enter a valid time in HH:MM format (e.g., 15:55).';
                return false;
            } else {
                input.classList.remove('is-invalid');
                errorElement.style.display = 'none';
                errorElement.textContent = '';
                return true;
            }
        }

        const billNumber = document.getElementById('bill-number');

        // Real-time format validation on input
        if (billNumber) {
            billNumber.addEventListener('input', function() {
                if (this.value && !/^[A-Za-z0-9]+$/.test(this.value)) {
                    showError(this, 'Bill number must be alphanumeric.');
                } else if (this.value.length > 50) {
                    showError(this, 'Bill number must not exceed 50 characters.');
                } else {
                    clearError(this);
                }
            });

            // Uniqueness check on blur (with SweetAlert for duplicates)
            billNumber.addEventListener('blur', async function() {
                if (this.value && /^[A-Za-z0-9]+$/.test(this.value) && this.value.length <= 50) {
                    try {
                        const response = await fetch(`/check-bill-number?bill_number=${encodeURIComponent(this.value)}&dispatch_id={{ $dispatch->id }}`, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.exists) {
                            showError(this, 'Bill number is already in use.');
                            Swal.fire({
                                icon: 'error',
                                title: 'Duplicate Bill Number',
                                text: 'This bill number is already in use. Please enter a unique bill number.',
                            });
                        } else {
                            clearError(this);
                        }
                    } catch (err) {
                        console.error('Error checking bill number:', err);
                        showError(this, 'Error checking bill number.');
                    }
                } else if (this.value) {
                    // Format errors already handled on input
                }
            });
        }

        async function handleSubmit(event) {
            event.preventDefault(); // Always prevent; submit manually if valid

            const recipientPincode = document.querySelector('input[name="recipient_pincode"]');
            const consigneePincode = document.querySelector('input[name="consignee_pincode"]');
            const dispatchOutTimeHidden = document.getElementById('dispatch_out_time_hidden');
            const warehouseSelect = document.querySelector('select[name="warehouse_id"]');
            let isValid = true;

            if (!validatePincode(recipientPincode, 'recipient-pincode-error')) {
                isValid = false;
            }
            if (!validatePincode(consigneePincode, 'consignee-pincode-error')) {
                isValid = false;
            }
            if (!validateTime(dispatchOutTimeHidden)) {
                isValid = false;
            }
            if (!warehouseSelect.value) {
                isValid = false;
                warehouseSelect.classList.add('is-invalid');
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a warehouse.',
                });
            } else {
                warehouseSelect.classList.remove('is-invalid');
            }

            // Bill number validation (format + uniqueness)
            if (billNumber?.value) {
                if (!/^[A-Za-z0-9]+$/.test(billNumber.value)) {
                    isValid = false;
                    showError(billNumber, 'Bill number must be alphanumeric.');
                } else if (billNumber.value.length > 50) {
                    isValid = false;
                    showError(billNumber, 'Bill number must not exceed 50 characters.');
                } else {
                    clearError(billNumber);
                    // Re-check uniqueness on submit
                    try {
                        const response = await fetch(`/check-bill-number?bill_number=${encodeURIComponent(billNumber.value)}&dispatch_id={{ $dispatch->id }}`, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.exists) {
                            isValid = false;
                            showError(billNumber, 'Bill number is already in use.');
                            // No additional Swal here, as it was shown on blur
                        } else {
                            clearError(billNumber);
                        }
                    } catch (err) {
                        isValid = false;
                        showError(billNumber, 'Error checking bill number.');
                    }
                }
            } else {
                clearError(billNumber);
            }

            // if (!isValid) {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Validation Error',
            //         text: 'Please correct the invalid fields before submitting.',
            //     });
            //     return;
            // }

            // All validations passed, submit the form
            if(isValid){
                document.getElementById('dispatch-form').submit();
            }
        }
        document.getElementById('dispatch-form').addEventListener('submit', handleSubmit);
    });
</script>
<script>
    $(document).ready(function () {
        const availableOrders = @json($orders);
        let selectedOrderIds = new Set([@foreach($items as $item)'{{ $item['order_id'] ?? '' }}', @endforeach]);
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        // ---------- DUPLICATE CHECK (Allocation + Size) ----------
        function isDuplicatePair(row) {
            const currentAllocation = row.find('.allocation-select').val();
            const currentSize = row.find('.size-select').val();
            if (!currentAllocation || !currentSize) return false;
            let isDup = false;
            $('#item-list-container tr').not(row).each(function () {
                const alloc = $(this).find('.allocation-select').val();
                const size = $(this).find('.size-select').val();
                if (alloc === currentAllocation && size === currentSize) {
                    isDup = true;
                    return false; // break
                }
            });
            return isDup;
        }
        // ---------- SIZE SELECT → Gauge Diff ----------
        $('#item-list-container').on('change', '.size-select', function () {
            const $select = $(this);
            const $row = $select.closest('tr');
            if (isDuplicatePair($row)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Entry',
                    text: 'Same Allocation + Size combination already exists.',
                });
                $select.val('');
                return;
            }
            const rate = $select.find('option:selected').data('rate') || 0;
            $row.find('input[name*="[gauge_diff]"]').val(rate.toFixed(2)).trigger('change');
        });
        // ---------- SERIAL NUMBERS ----------
        function updateSerialNumbers() {
            $('#item-list-container tr').each((i, el) => $(el).find('.sn').text(i + 1));
        }
        // ---------- REMOVE BUTTON VISIBILITY ----------
        function updateRemoveButtons() {
            const rows = $('#item-list-container tr');
            rows.find('.remove-item-row').show();
            if (rows.length === 1) rows.find('.remove-item-row').hide();
        }
        // ---------- ORDER SELECT → Load Allocations ----------
        $('#item-list-container').on('change', '.order-select', function () {
            const $select = $(this);
            const row = $select.closest('tr');
            const newId = $select.val();
            const prevId = $select.data('previous');
            if (prevId) selectedOrderIds.delete(prevId);
            if (newId) selectedOrderIds.add(newId);
            $select.data('previous', newId);
            // Reset allocation & dependent fields
            const allocationSelect = row.find('.allocation-select');
            allocationSelect.html('<option value="">Select Allocation</option>');
            row.find('.basic-price-input').val('');
            row.find('input[name*="[already_disp]"], input[name*="[remaining_qty]"], input[name*="[order_qty]"]').val('');
            row.find('input[name*="[token_amount]"]').val('N/A');
            row.find('.payment-term-select').val('').prop('disabled', true);
            row.find('input[name*="[payment_term]"][type="hidden"]').remove();
            if (!newId) {
                calculateRowTotal(row);
                updateGrandTotal();
                return;
            }
            fetch(`/orders/${newId}/allocations?remaining=true`, {
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                let index = 1;
                data.forEach(allocation => {
                    const tokenAmount = allocation.token_amount != null && !isNaN(parseFloat(allocation.token_amount))
                        ? parseFloat(allocation.token_amount).toFixed(2) : 'N/A';
                    const entityName = allocation.type === 'dealer' ? (allocation.dealer_name || 'N/A') : (allocation.distributor_name || 'N/A');
                    const entityCode = allocation.type === 'dealer' ? (allocation.dealer_code || 'N/A') : (allocation.distributor_code || 'N/A');
                    const optionText = `${index}) (For ${allocation.type}) : ${entityName} (${entityCode}) - Remaining: ${allocation.remaining_qty} MT`;
                    allocationSelect.append(
                        `<option value="${allocation.id}"
                                 data-remaining="${allocation.remaining_qty}"
                                 data-payment-term="${allocation.payment_terms || ''}"
                                 data-token-amount="${tokenAmount}">
                            ${optionText}
                        </option>`
                    );
                    index++;
                });
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: `Failed to load allocations: ${err.message}` });
            });
            calculateRowTotal(row);
            updateGrandTotal();
        });
        // ---------- ALLOCATION SELECT → Load Details ----------
        $('#item-list-container').on('change', '.allocation-select', function () {
            const row = $(this).closest('tr');
            const $select = $(this);
            const allocationId = $select.val();
            const selectedOption = $select.find('option:selected');
            const remainingQty = parseFloat(selectedOption.data('remaining')) || 0;
            const paymentTerm = selectedOption.data('payment-term') || '';
            const tokenAmount = selectedOption.data('token-amount') || 'N/A';
            // Reset dependent fields
            row.find('.basic-price-input').val('Loading...');
            row.find('input[name*="[already_disp]"]').val('');
            row.find('input[name*="[remaining_qty]"]').val(remainingQty.toFixed(2));
            row.find('input[name*="[order_qty]"]').val('');
            row.find('input[name*="[token_amount]"]').val(tokenAmount);
            row.find('.payment-term-select').val(paymentTerm).prop('disabled', true);
            row.find('input[name*="[payment_term]"][type="hidden"]').remove();
            // Add hidden input for form submission
            if (paymentTerm) {
                const name = row.find('.payment-term-select').attr('name');
                row.find('.payment-term-select').after(`<input type="hidden" name="${name}" value="${paymentTerm}">`);
            }
            if (!allocationId) {
                row.find('.basic-price-input').val('');
                row.find('input[name*="[already_disp]"], input[name*="[order_qty]"]').val('');
                calculateRowTotal(row);
                updateGrandTotal();
                return;
            }
            // Duplicate check
            if (isDuplicatePair(row)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Entry',
                    text: 'Same Allocation + Size already used.',
                });
                $select.val('');
                row.find('.basic-price-input').val('');
                row.find('input[name*="[token_amount]"]').val('N/A');
                row.find('.payment-term-select').val('').prop('disabled', true);
                row.find('input[name*="[payment_term]"][type="hidden"]').remove();
                calculateRowTotal(row);
                updateGrandTotal();
                return;
            }
            fetch(`/api/allocations/${allocationId}`, {
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                const basicPrice = data.agreed_basic_price != null ? parseFloat(data.agreed_basic_price) : 0;
                row.find('.basic-price-input').val(basicPrice.toFixed(2));
                row.find('input[name*="[already_disp]"]').val(data.dispatched_qty || 0);
                const already = parseFloat(row.find('input[name*="[already_disp]"]').val()) || 0;
                const remaining = parseFloat(row.find('input[name*="[remaining_qty]"]').val()) || 0;
                row.find('input[name*="[order_qty]"]').val((already + remaining).toFixed(2));
                row.find('input[name*="[loading_charge]"]').val(data.loading_charge || {{ $loadingCharge ?? 265 }});
                row.find('input[name*="[insurance]"]').val(data.insurance_charge || {{ $insuranceCharge ?? 40 }});
                row.find('input[name*="[gst]"]').val(data.gst_rate || {{ $gstRate ?? 18 }});
                // Token amount (override if API returns different)
                const apiToken = data.token_amount != null && !isNaN(parseFloat(data.token_amount))
                    ? parseFloat(data.token_amount).toFixed(2) : 'N/A';
                row.find('input[name*="[token_amount]"]').val(apiToken);
                calculateRowTotal(row);
                updateGrandTotal();
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: `Failed to load allocation: ${err.message}` });
                row.find('.basic-price-input').val('0.00');
                calculateRowTotal(row);
                updateGrandTotal();
            });
        });
        // ---------- CALCULATE ROW TOTAL ----------
        function calculateRowTotal(row) {
            const qty = parseFloat(row.find('input[name*="[dispatch_qty]"]').val()) || 0;
            const basicPrice = parseFloat(row.find('input[name*="[basic_price]"]').val()) || 0;
            const gaugeDiff = parseFloat(row.find('input[name*="[gauge_diff]"]').val()) || 0;
            const loading = parseFloat(row.find('input[name*="[loading_charge]"]').val()) || 0;
            const insurance = parseFloat(row.find('input[name*="[insurance]"]').val()) || 0;
            const gst = parseFloat(row.find('input[name*="[gst]"]').val()) || 0;
            let tokenAmount = row.find('input[name*="[token_amount]"]').val();
            tokenAmount = tokenAmount === 'N/A' ? 0 : parseFloat(tokenAmount) || 0;
            const finalPrice = basicPrice + gaugeDiff;
            row.find('input[name*="[final_price]"]').val(finalPrice.toFixed(2));
            const baseTotal = (finalPrice + loading + insurance) * qty;
            const gstAmt = baseTotal * (gst / 100);
            const total = baseTotal + gstAmt - tokenAmount;
            row.find('input[name*="[total_amount]"]').val(total.toFixed(2));
        }
        // ---------- GRAND TOTAL ----------
        function updateGrandTotal() {
            let total = 0;
            $('.item-total').each((i, el) => total += parseFloat($(el).val()) || 0);
            const addCharges = parseFloat($('#additional-charges').val()) || 0;
            total += addCharges;
            $('#grand-total').text("₹" + total.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
            $('#total-amount-hidden').val(total.toFixed(2));
        }
        $('#item-list-container').on('keyup change', '.item-calc', function () {
            calculateRowTotal($(this).closest('tr'));
            updateGrandTotal();
        });
        $('#additional-charges').on('keyup change', updateGrandTotal);
        // ---------- ADD ITEM ROW ----------
        $('#add-item-row').click(function () {
            const lastRow = $('#item-list-container tr:last');
            const newRow = lastRow.clone();
            const newIndex = $('#item-list-container tr').length;
            newRow.find('input, select').each(function () {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
                }
                if (!$(this).is('[readonly]') && !$(this).hasClass('payment-term-select')) {
                    $(this).val('');
                }
            });
            // Reset specific fields
            newRow.find('.order-select').val('').data('previous', '');
            newRow.find('.allocation-select').html('<option value="">Select Allocation</option>');
            newRow.find('.size-select').val('');
            newRow.find('input[name*="[dispatch_qty]"]').val('');
            newRow.find('input[name*="[basic_price]"], input[name*="[gauge_diff]"], input[name*="[final_price]"]').val('');
            newRow.find('input[name*="[already_disp]"], input[name*="[remaining_qty]"], input[name*="[order_qty]"]').val('');
            newRow.find('input[name*="[token_amount]"]').val('N/A');
            newRow.find('input[name*="[total_amount]"]').val('').attr('placeholder', 'Auto Calc');
            newRow.find('input[name*="[item_name]"]').val('{{ $singleItemName ?? 'TMT Bar' }}');
            newRow.find('input[name*="[length]"]').val('12');
            newRow.find('input[name*="[loading_charge]"]').val({{ $loadingCharge ?? 265 }});
            newRow.find('input[name*="[insurance]"]').val({{ $insuranceCharge ?? 40 }});
            newRow.find('input[name*="[gst]"]').val({{ $gstRate ?? 18 }});
            newRow.find('.payment-term-select').val('').prop('disabled', true);
            newRow.find('input[name*="[payment_term]"][type="hidden"]').remove();
            $('#item-list-container').append(newRow);
            updateSerialNumbers();
            updateRemoveButtons();
        });
        // ---------- REMOVE ITEM ROW ----------
        $('#item-list-container').on('click', '.remove-item-row', function () {
            const row = $(this).closest('tr');
            const orderId = row.find('.order-select').val();
            if (orderId) selectedOrderIds.delete(orderId);
            row.remove();
            updateSerialNumbers();
            updateRemoveButtons();
            updateGrandTotal();
        });
        // ---------- PREVENT PAYMENT TERM EDIT ----------
        $('#item-list-container').on('focus', '.payment-term-select', function () {
            $(this).blur(); // Prevent focus
        });
        // ---------- ATTACHMENT HANDLING ----------
        $(document).on('change', '.attachment-input', function () {
            const input = this;
            const $group = $(input).closest('.input-group');
            const $label = $group.find('.file-label');
            const $name = $group.find('.file-name');
            const $custom = $group.find('.custom-file-upload');
            const $hidden = $group.find('input[name$="[file_name]"]');
            const allowed = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (input.files[0]) {
                const file = input.files[0];
                if (!allowed.includes(file.type)) {
                    Swal.fire({ icon: 'error', title: 'Invalid Type', text: 'Only JPG, PNG, PDF, DOC, XLS allowed.' });
                    input.value = '';
                    $label.text('Choose file'); $name.text(''); $custom.removeClass('file-selected'); $hidden.val('');
                    return;
                }
                if (file.size > maxSize) {
                    Swal.fire({ icon: 'error', title: 'Too Large', text: 'Max 2MB allowed.' });
                    input.value = '';
                    $label.text('Choose file'); $name.text(''); $custom.removeClass('file-selected'); $hidden.val('');
                    return;
                }
                $label.text('Replace File');
                $name.text(file.name);
                $custom.addClass('file-selected');
                $hidden.val(file.name);
            } else {
                const original = $hidden.val();
                $label.text(original ? 'Replace File' : 'Choose file');
                $name.text(original || '');
                $custom.toggleClass('file-selected', !!original);
            }
        });
        // Restore existing file names on load
        $('.attachment-input').each(function () {
            const original = $(this).data('original-file') || $(this).closest('.input-group').find('input[name$="[file_name]"]').val();
            if (original) {
                $(this).closest('.input-group').find('.file-label').text('Replace File');
                $(this).closest('.input-group').find('.file-name').text(original);
                $(this).closest('.input-group').find('.custom-file-upload').addClass('file-selected');
            }
        });
        // ---------- ADD / REMOVE ATTACHMENT ROWS ----------
        $('#add-attachment-row').click(function () {
            const idx = $('#attachment-table-body tr').length;
            const row = `
                <tr class="attachment-row" data-index="${idx}">
                    <td>
                        <div class="input-group">
                            <label for="attachment_${idx}" class="custom-file-upload w-100">
                                <i class="fas fa-cloud-upload-alt me-1"></i>
                                <span class="file-label">Choose file</span>
                                <span class="file-name text-muted ms-2"></span>
                            </label>
                            <input type="file" name="attachments[${idx}][document]" id="attachment_${idx}" class="attachment-input">
                            <input type="hidden" name="attachments[${idx}][file_name]" value="">
                        </div>
                    </td>
                    <td><input type="text" name="attachments[${idx}][remark]" class="form-control" placeholder="Remark"></td>
                    <td class="action-cell">
                        <button type="button" class="btn btn-sm btn-danger remove-attachment-row"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>`;
            $('#attachment-table-body').append(row);
            updateAttachmentRemoveButtons();
        });
        $('#attachment-table-body').on('click', '.remove-attachment-row', function () {
            $(this).closest('tr').remove();
            updateAttachmentRemoveButtons();
        });
        function updateAttachmentRemoveButtons() {
            const rows = $('#attachment-table-body tr');
            if (rows.length === 0) $('#add-attachment-row').click();
            rows.find('.remove-attachment-row').toggle(rows.length > 1);
        }
        // ---------- INIT ----------
        updateSerialNumbers();
        updateRemoveButtons();
        updateAttachmentRemoveButtons();
        updateGrandTotal();
        // Ensure all existing payment terms are disabled + hidden input present
        $('#item-list-container .payment-term-select').each(function () {
            const val = $(this).val();
            if (val) {
                $(this).prop('disabled', true);
                const name = $(this).attr('name');
                if (!$(this).siblings(`input[type="hidden"][name="${name}"]`).length) {
                    $(this).after(`<input type="hidden" name="${name}" value="${val}">`);
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemListContainer = document.getElementById('item-list-container');
        itemListContainer.addEventListener('input', function (e) {
            if (e.target.matches('input[name*="[dispatch_qty]"]')) {
                const row = e.target.closest('tr');
                const orderId = row.querySelector('.order-select').value;
                const dispatchQty = parseFloat(e.target.value) || 0;
                if (orderId) {
                    let totalDispatch = 0;
                    let totalRemaining = 0;
                    itemListContainer.querySelectorAll('tr').forEach(otherRow => {
                        if (otherRow.querySelector('.order-select').value === orderId) {
                            totalDispatch += parseFloat(otherRow.querySelector('.dispatch-qty').value) || 0;
                            totalRemaining += parseFloat(otherRow.querySelector('.remaining-qty').value) || 0;
                        }
                    });
                    const maxAllowed = totalRemaining + 5;
                    if (totalDispatch > maxAllowed) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Quantity',
                            text: `Total dispatch quantity for this order cannot exceed total remaining quantity + 5 MT (Max: ${maxAllowed.toFixed(2)} MT).`,
                        });
                        e.target.value = '';
                    } else {
                        calculateRowTotal(row);
                        updateGrandTotal();
                    }
                }
            }
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
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        function loadCities(stateSelector, citySelector, selectedCityId) {
            let stateId = $(stateSelector).val();
            let $cityDropdown = $(citySelector);
            if (!stateId) {
                $cityDropdown.html('<option value="">-- Select City --</option>');
                return;
            }
            $cityDropdown.html('<option value="">Loading...</option>');
            $.ajax({
                url: `/get-cities/${stateId}`,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (data) {
                    $cityDropdown.empty().append('<option value="">-- Select City --</option>');
                    let foundSelected = false;
                    data.forEach(function (city) {
                        let selected = city.id.toString() === selectedCityId?.toString() ? 'selected' : '';
                        if (selected) foundSelected = true;
                        $cityDropdown.append(`<option value="${city.id}" ${selected}>${city.name}</option>`);
                    });
                    // if (selectedCityId && !foundSelected) {
                    // Swal.fire({
                    // icon: 'warning',
                    // title: 'City Not Found',
                    // text: `The city with ID ${selectedCityId} was not found for the selected state.`,
                    // });
                    // }
                },
                error: function (err) {
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
            loadCities('#state', '#city', '{{ old('recipient_city', $dispatch->recipient_city_id) }}');
        });
        $('#state2').on('change', function () {
            loadCities('#state2', '#city2', '{{ old('consignee_city', $dispatch->consignee_city_id) }}');
        });
        $('#state').trigger('change');
        $('#state2').trigger('change');
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
