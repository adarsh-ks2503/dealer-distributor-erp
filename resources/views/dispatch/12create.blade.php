@extends('layouts.main')
@section('title', 'Order Create - Singhal')

@section('content')
<main class="main" id="main">
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

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="basic-info-card p-4 shadow-sm rounded-3 d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Add New Order - {{ ucfirst($type) }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('order_management') }}">Order</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('order_management') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <form action="{{ route('order_management.store') }}" method="POST" enctype="multipart/form-data" id="order-form">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            @if ($type == 'dealer')
                <input type="hidden" name="dealer_id" value="{{ old('dealer_id', $party->id) }}">
            @endif
            @if ($type == 'distributor')
                <input type="hidden" name="distributor_id" value="{{ old('distributor_id', $party->id) }}">
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
                                        <input required name="recipient_pincode" type="text" class="form-control" placeholder="Pincode" pattern="[0-9]{6}" maxlength="6" oninput="validatePincode(this, 'recipient-pincode-error')" value="{{ old('recipient_pincode', $party->pincode) }}">
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
                                        <input required name="consignee_pincode" type="text" class="form-control" placeholder="Pincode" pattern="[0-9]{6}" maxlength="6" oninput="validatePincode(this, 'consignee-pincode-error')" value="{{ old('consignee_pincode', $party->pincode) }}">
                                        <span id="consignee-pincode-error" class="text-danger"></span>
                                    </div>
                                    <input required name="consignee_mobile_no" type="number" class="form-control" placeholder="Mobile Number" value="{{ old('consignee_mobile_no', $party->mobile_no) }}">
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
                            <label class="form-label">Dispatch Number</label>
                            <input id="dispatch_number" name="dispatch_number" type="text" class="form-control" readonly value="Auto-Generated">
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
                            <label class="form-label">Bill No <span class="required-asterisk">*</span></label>
                            <input name="bill_number" id="bill-number" type="text" class="form-control" placeholder="Bill Number" required value="{{ old('bill_number') }}">
                            <span id="bill-number-error" class="text-danger"></span>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dispatch Out Time</label>
                            <input name="dispatch_out_time" type="time" class="form-control" value="{{ old('dispatch_out_time') }}">
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
                            <label class="form-label">Transporter Name <span class="required-asterisk">*</span></label>
                            <input name="transporter_name" type="text" class="form-control" required value="{{ old('transporter_name') }}">
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
                            <input name="driver_mobile_no" type="text" class="form-control" value="{{ old('driver_mobile_no') }}">
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
                                    <th>Length (ft) <span class="required-asterisk">*</span></th>
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
                                    <td><input type="text" value="{{ old('items.0.length', '12') }}" name="items[0][length]" class="form-control form-control-sm" placeholder="Enter"></td>
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

            <div class="card mt-3">
                <div class="card-header"><i class="fas fa-file-contract me-2"></i>Terms & Conditions</div>
                <div class="card-body">
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
                            <label class="form-label">Terms & Conditions</label>
                            <textarea name="terms_conditions" id="terms_conditions" class="form-control" rows="10">{!! old('terms_conditions', $defaultTerms) !!}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachment Section -->
            <div class="card mb-4 shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle me-2">
                            <i class="bi bi-paperclip text-white"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Attachment List</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" id="attachment_table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:35%"><strong>Attachment</strong></th>
                                    <th style="width:45%"><strong>Remarks</strong></th>
                                    <th class="text-center"><strong>Action</strong></th>
                                </tr>
                            </thead>
                            <tbody id="attachment-table-body">
                                @php
                                    $oldAttachments = old('attachments', [[]]);
                                    $sessionAttachments = session()->get('preserved_attachments', []);
                                @endphp
                                @foreach ($oldAttachments as $index => $attachment)
                                    <tr class="attachment-row" data-index="{{ $index }}">
                                        <td>
                                            <div class="input-group">
                                                <label for="attachment_{{ $index }}" class="custom-file-upload w-100 {{ isset($sessionAttachments[$index]) ? 'file-selected' : '' }}">
                                                    <i class="fas fa-cloud-upload-alt me-1"></i>
                                                    <span class="file-label">{{ isset($sessionAttachments[$index]) ? 'Replace File' : 'Choose file' }}</span>
                                                    <span class="file-name text-muted ms-2">
                                                        @if (isset($sessionAttachments[$index]))
                                                            {{ $sessionAttachments[$index]['name'] }}
                                                        @endif
                                                    </span>
                                                </label>
                                                <input type="file" name="attachments[{{ $index }}][document]" id="attachment_{{ $index }}" class="attachment-input">
                                                @if (isset($sessionAttachments[$index]))
                                                    <input type="hidden" name="attachments[{{ $index }}][temp_path]" value="{{ $sessionAttachments[$index]['temp_path'] }}">
                                                    <input type="hidden" name="attachments[{{ $index }}][name]" value="{{ $sessionAttachments[$index]['name'] }}">
                                                @endif
                                                <small class="form-text text-muted">
                                                    @if (isset($sessionAttachments[$index]))
                                                        Preserved file: {{ $sessionAttachments[$index]['name'] }}
                                                    @endif
                                                </small>
                                            </div>
                                            @error('attachments.' . $index . '.document')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <textarea name="attachments[{{ $index }}][remark]" rows="1" class="form-control">{{ old('attachments.' . $index . '.remark', $attachment['remark'] ?? '') }}</textarea>
                                            @error('attachments.' . $index . '.remark')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-attachment-row">
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <button type="button" class="btn btn-sm btn-success" id="add_attachment_row">
                                            <i class="fas fa-plus-circle"></i> Add Row
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-end pt-3">
                <a href="{{ route('order_management') }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>

        <!-- Alert Modal -->
        <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="alertModalLabel">Error</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="alertModalBody">
                        <!-- Message will be injected via JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/j79p76q3019i18w7ploly2ydi03tbrliogi31x8ezwlobn7h/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#terms_conditions',
        plugins: 'lists link image table code',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | code',
        menubar: false,
        height: 300,
    });
</script>

<!-- Select2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('styles')
<style>
    .basic-info-card {
        background: #fff;
        border-radius: 12px;
        transition: all 0.25s ease-in-out;
    }
    .basic-info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .icon-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .breadcrumb {
        font-size: 13px;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
    }
    .info-block {
        display: flex;
        flex-direction: column;
        padding: 12px;
        border: 1px solid #f1f1f1;
        border-radius: 8px;
        background: #fafafa;
        height: 100%;
        transition: background 0.2s;
    }
    .info-block:hover {
        background: #f3f4f6;
    }
    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 14px;
        font-weight: 500;
        color: #111827;
        line-height: 1.4;
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
</style>

<style>
    #alertModal .modal-content {
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    #alertModal .modal-body {
        font-size: 16px;
        line-height: 1.6;
    }
    #alertModal ul {
        padding-left: 1.2rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if ($errors->any())
            let errorMessages = `<ul class="mb-0">`;
            @foreach ($errors->all() as $error)
                errorMessages += `<li>{{ $error }}</li>`;
            @endforeach
            errorMessages += `</ul>`;
            showAlertModal(errorMessages, 'Validation Errors', 'danger');
        @endif
        @if (Session::has('error'))
            showAlertModal("{{ Session::get('error') }}", 'Error', 'danger');
        @endif
        @if (Session::has('success'))
            showAlertModal("{{ Session::get('success') }}", 'Success', 'success');
        @endif

        function showAlertModal(message, title = 'Alert', type = 'info') {
            const modalTitle = document.getElementById('alertModalLabel');
            const modalBody = document.getElementById('alertModalBody');
            const modalHeader = document.querySelector('#alertModal .modal-header');

            modalTitle.textContent = title;
            modalBody.innerHTML = message;
            modalHeader.className = 'modal-header text-white';
            switch (type) {
                case 'success':
                    modalHeader.classList.add('bg-success');
                    break;
                case 'danger':
                    modalHeader.classList.add('bg-danger');
                    break;
                case 'warning':
                    modalHeader.classList.add('bg-warning');
                    modalHeader.classList.add('text-dark');
                    break;
                default:
                    modalHeader.classList.add('bg-primary');
            }
            const modal = new bootstrap.Modal(document.getElementById('alertModal'));
            modal.show();
        }
    });

    $(document).ready(function () {
        @if ($type == 'distributor')
            let distRowCount = {{ count($orders) }};

            $('.dealer-select').select2();

            $('#add_dist_row').click(function () {
                const tbody = $('#dist_order_table tbody');
                const newRow = $(`
                    <tr>
                        <td>
                            <select name="orders[${distRowCount}][for_type]" class="form-select for-type-select" required>
                                <option value="">Select</option>
                                <option value="self">Self</option>
                                <option value="dealer">Dealer</option>
                            </select>
                        </td>
                        <td>
                            <select name="orders[${distRowCount}][dealer_id]" class="form-select dealer-select" disabled>
                                <option value="">Select Dealer</option>
                                @foreach ($assignedDealers as $dealer)
                                    <option value="{{ $dealer->id }}">{{ $dealer->name }} ({{ $dealer->code }}) - Order Limit: {{ $dealer->order_limit }} MT, Allowed: {{ $dealer->allowed_order_limit }} MT</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input step="0.001" type="number" name="orders[${distRowCount}][order_qty]" class="form-control qty-input" required>
                        </td>
                        <td>
                            <input type="text" name="orders[${distRowCount}][basic_price]" class="form-control price-input"
                                value="{{ $basicPrice->distributor_basic_price }}" readonly required>
                        </td>
                        <td>
                            <input step="0.01" type="number" name="orders[${distRowCount}][agreed_basic_price]" class="form-control price-input"
                                value="{{ $basicPrice->distributor_basic_price }}" required>
                        </td>
                        <td>
                            <input step="0.01" type="number" name="orders[${distRowCount}][token_amount]" class="form-control">
                        </td>
                        <td>
                            <select name="orders[${distRowCount}][payment_term]" class="form-select" required>
                                <option value="">Select Payment Term</option>
                                <option value="Advance">Advance</option>
                                <option value="Next Day">Next Day</option>
                                <option value="15 Days Later">15 Days Later</option>
                                <option value="30 Days Later">30 Days Later</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="orders[${distRowCount}][remarks]" class="form-control" rows="1"></textarea>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-dist-row">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                        </td>
                    </tr>
                `);
                tbody.append(newRow);
                newRow.find('.dealer-select').select2();
                distRowCount++;
                updateRemoveButtons();
            });

            $('#dist_order_table').on('change', '.for-type-select', function () {
                const row = $(this).closest('tr');
                const dealerSelect = row.find('.dealer-select');
                dealerSelect.prop('disabled', this.value !== 'dealer');
                if (dealerSelect.prop('disabled')) {
                    dealerSelect.val('').trigger('change');
                }
            });

            $('#dist_order_table').on('click', '.remove-dist-row', function () {
                $(this).closest('tr').remove();
                distRowCount--;
                updateRemoveButtons();
            });

            function updateRemoveButtons() {
                let rows = $('#dist_order_table tbody tr');
                rows.find('.remove-dist-row').show();
                if (rows.length === 1) {
                    rows.find('.remove-dist-row').hide();
                }
            }

            updateRemoveButtons();
        @endif

        let attachmentRowCount = {{ count($oldAttachments) }};

        $('#add_attachment_row').click(function () {
            const tbody = $('#attachment-table-body');
            const newRow = $(`
                <tr class="attachment-row" data-index="${attachmentRowCount}">
                    <td>
                        <div class="input-group">
                            <label for="attachment_${attachmentRowCount}" class="custom-file-upload w-100">
                                <i class="fas fa-cloud-upload-alt me-1"></i>
                                <span class="file-label">Choose file</span>
                                <span class="file-name text-muted ms-2"></span>
                            </label>
                            <input type="file" name="attachments[${attachmentRowCount}][document]" id="attachment_${attachmentRowCount}" class="attachment-input">
                        </div>
                    </td>
                    <td>
                        <textarea name="attachments[${attachmentRowCount}][remark]" rows="1" class="form-control"></textarea>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-attachment-row">
                            <i class="fas fa-minus-circle"></i>
                        </button>
                    </td>
                </tr>
            `);
            tbody.append(newRow);
            attachmentRowCount++;
            updateAttachmentRemoveButtons();
        });

        $('#attachment-table-body').on('click', '.remove-attachment-row', function () {
            $(this).closest('tr').remove();
            attachmentRowCount--;
            updateAttachmentRemoveButtons();
        });

        function updateAttachmentRemoveButtons() {
            let rows = $('#attachment-table-body tr');
            if (rows.length === 0) {
                $('#add_attachment_row').click();
            }
            rows.find('.remove-attachment-row').show();
            if (rows.length === 1) {
                rows.find('.remove-attachment-row').hide();
            }
        }

        $(document).on('change', '.attachment-input', function () {
            const input = this;
            const $inputGroup = $(this).closest('.input-group');
            const $label = $inputGroup.find('.file-label');
            const $fileName = $inputGroup.find('.file-name');
            const $customUpload = $inputGroup.find('.custom-file-upload');
            const $formText = $inputGroup.find('.form-text');
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            const maxSize = 2048 * 1024; // 2MB in bytes

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
                    $formText.text('');
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
                    $formText.text('');
                    $customUpload.removeClass('file-selected');
                    return;
                }
                $label.text('File selected:');
                $fileName.text(file.name);
                $formText.text('');
                $customUpload.addClass('file-selected');
                console.log('File selected:', {
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    inputId: input.id,
                    inputName: input.name
                });
            } else {
                $label.text('Choose file');
                $fileName.text('');
                $formText.text('');
                $customUpload.removeClass('file-selected');
                console.log('No file selected for input:', input.id);
            }
        });

        // Initialize attachment remove buttons
        updateAttachmentRemoveButtons();

        // Log form data on submit for debugging
        $('#order-form').on('submit', function (e) {
            const formData = new FormData(this);
            console.log('Form submission data:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}:`, value instanceof File ? `File(${value.name}, ${value.size} bytes, ${value.type})` : value);
            }
        });

        updateAttachmentRemoveButtons();

        $('#order-form').on('submit', function (e) {
            const formData = new FormData(this);
            console.log('Form submission data:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}:`, value instanceof File ? `File(${value.name}, ${value.size} bytes, ${value.type})` : value);
            }
        });
    });
</script>
@endpush
