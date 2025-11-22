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

        <form id="orderForm" action="{{ route('order_management.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="type" value="{{ $type }}">
            @if ($type == 'dealer')
                <input type="hidden" name="dealer_id" value="{{ old('dealer_id', $party->id) }}">
            @endif
            @if ($type == 'distributor')
                <input type="hidden" name="distributor_id" value="{{ old('distributor_id', $party->id) }}">
            @endif

            <div class="row">
                <!-- Left Column: Basic Information -->
                <div class="col-md-6 mb-4">
                    <div class="basic-info-card p-4 shadow-sm rounded-3 h-100">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle me-2">
                                <i class="bi bi-person-fill text-white"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Basic Information</h6>
                        </div>
                        <div class="row g-3">
                            @php
                                $basicInfo = [
                                    ['label' => 'Name', 'value' => $party->name],
                                    ['label' => 'Code', 'value' => $party->code],
                                    ['label' => 'Mobile', 'value' => $party->mobile_no],
                                    ['label' => 'Email', 'value' => $party->email],
                                    ['label' => 'Order Type', 'value' => ucfirst($type), 'class' => 'text-success fw-semibold'],
                                    ['label' => 'GST No', 'value' => $party->gst_num],
                                    ['label' => 'PAN', 'value' => $party->pan_num],
                                    ['label' => 'Order Limit (MT)', 'value' => $party->order_limit, 'class' => 'text-danger fw-semibold'],
                                    ['label' => 'Allowed Order Limit (MT)', 'value' => $party->allowed_order_limit, 'class' => 'text-success fw-semibold'],
                                    ['label' => 'Individual Allowed Order Limit (MT)', 'value' => $type == 'distributor' ? $party->individual_allowed_order_limit : 'N/A', 'class' => 'text-success fw-semibold', 'condition' => $type == 'distributor'],
                                ];
                            @endphp

                            @foreach ($basicInfo as $item)
                                @if (!isset($item['condition']) || $item['condition'])
                                    <div class="col-6">
                                        <div class="info-block h-100">
                                            <span class="info-label">{{ $item['label'] }}</span>
                                            <span class="info-value {{ $item['class'] ?? '' }}">
                                                {{ $item['value'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Address -->
                <div class="col-md-6 mb-4">
                    <div class="basic-info-card p-4 shadow-sm rounded-3 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle me-2 bg-success">
                                <i class="bi bi-geo-alt-fill text-white"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Address Details</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-block">
                                    <span class="info-label">Address</span>
                                    <textarea name="address" rows="2" class="form-control" readonly>{{ old('address', $party->address) }}</textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-block">
                                    <span class="info-label">Pincode</span>
                                    <input type="text" name="pincode" class="form-control" readonly value="{{ old('pincode', $party->pincode) }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-block">
                                    <span class="info-label">City</span>
                                    <input type="text" name="city" class="form-control" readonly value="{{ old('city', $party->city->name ?? '') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-block">
                                    <span class="info-label">State</span>
                                    <input type="text" name="state" class="form-control" readonly value="{{ old('state', $party->state->state ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details Section -->
            <div class="basic-info-card p-4 shadow-sm rounded-3 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle me-2 bg-warning">
                        <i class="bi bi-receipt-cutoff text-white"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Order Details</h6>
                </div>

                @if ($type == 'dealer')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-block">
                                <span class="info-label">Order No <span class="text-danger">*</span></span>
                                <input id="order_number" name="order_number" type="text" class="form-control"
                                    value="{{ old('order_number', $orderNumber) }}" readonly required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block">
                                <span class="info-label">Order Date <span class="text-danger">*</span></span>
                                <input type="date" name="order_date" class="form-control" required
                                    value="{{ old('order_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block">
                                <span class="info-label">Today Basic Price (MT)</span>
                                <input readonly name="today_price" type="text" class="form-control"
                                    value="{{ old('today_price', $basicPrice->dealer_basic_price) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block">
                                <span class="info-label">Created By <span class="text-danger">*</span></span>
                                <input name="created_by" type="text" class="form-control" readonly required
                                    value="{{ old('created_by', Auth::user()->name . ' ' . Auth::user()->last_name) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block">
                                <span class="info-label">Loading Charge (₹)<span class="text-danger">*</span></span>
                                <input readonly name="loading_charge" type="text" class="form-control"
                                    value="265">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block">
                                <span class="info-label">Insurance Charge (₹)<span class="text-danger">*</span></span>
                                <input readonly name="insurance_charge" type="text" class="form-control"
                                    value="40">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row my-3 mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Order Quantity (MT)<span class="text-danger">*</span></label>
                                <input
                                    type="number"
                                    step="0.1"
                                    name="orders[0][order_qty]"
                                    value="{{ old('orders.0.order_qty') }}"
                                    class="form-control"
                                    required
                                    data-max-limit="{{ $party->allowed_order_limit }}"
                                >
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Basic Price <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    name="orders[0][basic_price]"
                                    value="{{ old('orders.0.basic_price', $basicPrice->dealer_basic_price ?? 0) }}"
                                    class="form-control"
                                    readonly
                                >
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Agreed Basic Price<span class="text-danger">*</span></label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="orders[0][agreed_basic_price]"
                                    value="{{ old('orders.0.agreed_basic_price', $basicPrice->dealer_basic_price ?? 0) }}"
                                    class="form-control"
                                    required
                                >
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Token Amount</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="orders[0][token_amount]"
                                    value="{{ old('orders.0.token_amount') }}"
                                    class="form-control"
                                >
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <input type="hidden" name="orders[0][for_type]" value="self">
                        <input type="hidden" name="orders[0][dealer_id]" value="{{ $party->id }}">
                        <div class="col-md-3 info-block">
                            <label class="info-label">Payment Term <span class="text-danger">*</span></label>
                            <select name="orders[0][payment_term]" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Advance" {{ old('orders.0.payment_term') == 'Advance' ? 'selected' : '' }}>Advance</option>
                                <option value="Next Day" {{ old('orders.0.payment_term') == 'Next Day' ? 'selected' : '' }}>Next Day</option>
                                <option value="15 Days Later" {{ old('orders.0.payment_term') == '15 Days Later' ? 'selected' : '' }}>15 Days Later</option>
                                <option value="30 Days Later" {{ old('orders.0.payment_term') == '30 Days Later' ? 'selected' : '' }}>30 Days Later</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-9">
                            <div class="info-block">
                                <span class="info-label">Remark</span>
                                <textarea name="orders[0][remarks]" rows="2" class="form-control">{{ old('orders.0.remarks') }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="table-responsive" style="overflow-x: auto;">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="info-label">Order Number <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="order_number"
                                    id="order_number"
                                    class="form-control"
                                    value="{{ old('order_number', $orderNumber) }}"
                                    readonly
                                    style="white-space: nowrap; overflow-x: auto; font-family: monospace;">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="info-label">Order Date <span class="text-danger">*</span></label>
                                <input type="date"
                                    name="order_date"
                                    class="form-control"
                                    value="{{ old('order_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="info-label">Insurance Charge <span class="text-danger">*</span></label>
                                <input type="number"
                                    name="insurance_charge"
                                    class="form-control"
                                    value="40"
                                    required
                                    readonly>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="info-label">Loading Charge <span class="text-danger">*</span></label>
                                <input type="number"
                                    name="loading_charge"
                                    class="form-control"
                                    value="265"
                                    required
                                    readonly>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="info-label">Remarks</label>
                                <input type="text"
                                    name="remarks"
                                    class="form-control"
                                    value="{{ old('remarks') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Created By <span class="text-danger">*</span></div>
                                <input name="created_by" type="text" class="form-control" readonly required
                                    value="{{ old('created_by', Auth::user()->name . ' ' . Auth::user()->last_name) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <table class="table table-bordered" id="dist_order_table" style="min-width: 1200px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Order For<span class="text-danger">*</span></th>
                                    <th>Dealer (if applicable)</th>
                                    <th>Order Qty (MT)<span class="text-danger">*</span></th>
                                    <th>Basic Price (MT)</th>
                                    <th>Agreed Price (MT)<span class="text-danger">*</span></th>
                                    <th>Token Amount</th>
                                    <th>Payment Term<span class="text-danger">*</span></th>
                                    <th>Remarks</th>
                                    <th style="width:5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $orders = old('orders', [[]]);
                                @endphp
                                @foreach($orders as $i => $order)
                                    <tr>
                                        <td>
                                            <select name="orders[{{ $i }}][for_type]" class="form-select for-type-select" required>
                                                <option value="">Select</option>
                                                <option value="self" {{ ($order['for_type'] ?? '') == 'self' ? 'selected' : '' }}>Self</option>
                                                <option value="dealer" {{ ($order['for_type'] ?? '') == 'dealer' ? 'selected' : '' }}>Dealer</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <select name="orders[{{ $i }}][dealer_id]"
                                                    class="form-select dealer-select"
                                                    {{ ($order['for_type'] ?? '') != 'dealer' ? 'disabled' : '' }}>
                                                <option value="">Select Dealer</option>
                                                @foreach ($assignedDealers as $dealer)
                                                    <option value="{{ $dealer->id }}"
                                                        data-allowed-limit="{{ $dealer->allowed_order_limit }}"
                                                        {{ ($order['dealer_id'] ?? '') == $dealer->id ? 'selected' : '' }}>
                                                        {{ $dealer->name }} ({{ $dealer->code }}) - Order Limit: {{ $dealer->order_limit }} MT, Allowed: {{ $dealer->allowed_order_limit }} MT
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <input step="0.001" type="number" name="orders[{{ $i }}][order_qty]"
                                                class="form-control qty-input"
                                                value="{{ $order['order_qty'] ?? '' }}" required
                                                data-max-limit="{{ $type == 'distributor' && ($order['for_type'] ?? '') == 'self' ? $party->individual_allowed_order_limit : '' }}">
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <input type="text" name="orders[{{ $i }}][basic_price]"
                                                class="form-control price-input"
                                                value="{{ $order['basic_price'] ?? $basicPrice->distributor_basic_price }}" readonly required>
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <input step="0.01" type="number" name="orders[{{ $i }}][agreed_basic_price]"
                                                class="form-control price-input"
                                                value="{{ $order['agreed_basic_price'] ?? $basicPrice->distributor_basic_price }}" required>
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <input step="0.01" type="number" name="orders[{{ $i }}][token_amount]"
                                                class="form-control"
                                                value="{{ $order['token_amount'] ?? '' }}">
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <select name="orders[{{ $i }}][payment_term]" class="form-select" required>
                                                <option value="">Select Payment Term</option>
                                                <option value="Advance" {{ ($order['payment_term'] ?? '') == 'Advance' ? 'selected' : '' }}>Advance</option>
                                                <option value="Next Day" {{ ($order['payment_term'] ?? '') == 'Next Day' ? 'selected' : '' }}>Next Day</option>
                                                <option value="15 Days Later" {{ ($order['payment_term'] ?? '') == '15 Days Later' ? 'selected' : '' }}>15 Days Later</option>
                                                <option value="30 Days Later" {{ ($order['payment_term'] ?? '') == '30 Days Later' ? 'selected' : '' }}>30 Days Later</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <textarea name="orders[{{ $i }}][remarks]" class="form-control" rows="1">{{ $order['remarks'] ?? '' }}</textarea>
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-dist-row">
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <button type="button" class="btn btn-sm btn-success" id="add_dist_row">
                                            <i class="fas fa-plus-circle"></i> Add Row
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Terms & Conditions -->
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
                            <tbody>
                                @php
                                    $tempAttachments = session('temp_attachments', []);
                                    $atchRemarks = old('atch_remarks', []);
                                    $maxIndex = max(count($atchRemarks), count($tempAttachments)) ?: 1;
                                @endphp
                                @for ($index = 0; $index < $maxIndex; $index++)
                                    <tr>
                                        <td>
                                            @if (isset($tempAttachments[$index]))
                                                <p class="mb-1">Uploaded: {{ $tempAttachments[$index]['original_name'] }}</p>
                                                <input type="hidden" name="temp_attachments[]" value="{{ $tempAttachments[$index]['path'] }}">
                                                <input type="file" name="attachments[]" class="form-control" title="Replace file (optional)">
                                            @else
                                                <input type="file" name="attachments[]" class="form-control">
                                            @endif
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td>
                                            <textarea name="atch_remarks[]" rows="1" class="form-control">{{ $atchRemarks[$index] ?? '' }}</textarea>
                                            <div class="invalid-feedback"></div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-attachment-row">
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
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
        <div class="modal fade" id="sweetAlertModal" tabindex="-1" aria-labelledby="sweetAlertModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="sweetAlertModalLabel">Error</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="sweetAlertModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Summernote CSS & JS -->
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-lite.js"></script>
@endpush

<!-- Select2 & SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    const type = '{{ $type }}';
    const selfAllowedLimit = {{ $type == 'distributor' ? $party->individual_allowed_order_limit : ($type == 'dealer' ? $party->allowed_order_limit : 0) }};
    let distRowCount = {{ count(old('orders', [[]])) }};
    let attachmentLastId = {{ max(count(old('atch_remarks', [])), count(session('temp_attachments', []))) ?: 1 }};

    $(document).ready(function() {
        // Initialize Summernote
        $('#terms_conditions').summernote({
            placeholder: 'Enter terms and conditions...',
            height: 250,
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

        // Initialize Select2
        $('.dealer-select').select2();

        // Add Distributor Row
        $('#add_dist_row').on('click', function () {
            const tbody = $('#dist_order_table tbody');
            const newRow = $(`
                <tr>
                    <td>
                        <select name="orders[${distRowCount}][for_type]" class="form-select for-type-select" required>
                            <option value="">Select</option>
                            <option value="self">Self</option>
                            <option value="dealer">Dealer</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <select name="orders[${distRowCount}][dealer_id]" class="form-select dealer-select" disabled>
                            <option value="">Select Dealer</option>
                            @foreach ($assignedDealers as $dealer)
                                <option value="{{ $dealer->id }}" data-allowed-limit="{{ $dealer->allowed_order_limit }}">{{ $dealer->name }} ({{ $dealer->code }}) - Allowed: {{ $dealer->allowed_order_limit }} MT</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <input step="0.001" type="number" name="orders[${distRowCount}][order_qty]" class="form-control qty-input" required>
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <input type="text" name="orders[${distRowCount}][basic_price]" class="form-control price-input"
                            value="{{ $basicPrice->distributor_basic_price }}" required readonly>
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <input step="0.01" type="number" name="orders[${distRowCount}][agreed_basic_price]" class="form-control price-input"
                            value="{{ $basicPrice->distributor_basic_price }}" required>
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <input step="0.01" type="number" name="orders[${distRowCount}][token_amount]" class="form-control">
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <select name="orders[${distRowCount}][payment_term]" class="form-select" required>
                            <option value="">Select Payment Term</option>
                            <option value="Advance">Advance</option>
                            <option value="Next Day">Next Day</option>
                            <option value="15 Days Later">15 Days Later</option>
                            <option value="30 Days Later">30 Days Later</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <textarea name="orders[${distRowCount}][remarks]" class="form-control" rows="1"></textarea>
                        <div class="invalid-feedback"></div>
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
        });

        // For Type Change
        $(document).on('change', '.for-type-select', function () {
            const row = $(this).closest('tr');
            const dealerSelect = row.find('.dealer-select');
            dealerSelect.prop('disabled', this.value !== 'dealer');
            if (dealerSelect.prop('disabled')) dealerSelect.val('').trigger('change');
            row.find('.qty-input').data('max-limit', this.value === 'self' ? selfAllowedLimit : '');
        });

        $(document).on('change', '.dealer-select', function () {
            const selectedOption = $(this).find('option:selected');
            const maxLimit = parseFloat(selectedOption.data('allowed-limit')) || 0;
            const qtyInput = $(this).closest('tr').find('.qty-input');
            qtyInput.data('max-limit', maxLimit);

            // Optional: Clear qty if limit is 0
            if (maxLimit === 0) {
                qtyInput.val('');
            }
        });

        $(document).on('click', '.remove-dist-row', function () {
            $(this).closest('tr').remove();
        });

        // Attachment Rows
        $('#add_attachment_row').on('click', function () {
            const tbody = $('#attachment_table tbody');
            tbody.append(`
                <tr>
                    <td><input type="file" name="attachments[]" class="form-control"><div class="invalid-feedback"></div></td>
                    <td><textarea name="atch_remarks[]" rows="1" class="form-control"></textarea><div class="invalid-feedback"></div></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-attachment-row"><i class="fas fa-minus-circle"></i></button></td>
                </tr>
            `);
        });

        $(document).on('click', '.remove-attachment-row', function () {
            $(this).closest('tr').remove();
        });

        // Form Submit
        $('#orderForm').on('submit', function(e) {
            e.preventDefault();
            if (!validateForm()) return;

            // Save Summernote content
            $('#terms_conditions').val($('#terms_conditions').summernote('code'));

            const formData = new FormData(this);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        localStorage.setItem('successMessage', response.message);
                        window.location.href = response.redirect_url;
                    } else {
                        showSweetAlert('Something went wrong.', 'Error', 'error');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) window.location.href = '/login';
                    else if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        clearErrors();
                        $.each(errors, function(key, message) {
                            const field = $('[name="' + key.replace(/\./g, '\\.') + '"]');
                            if (field.length) showError(field, message[0]);
                            else showSweetAlert(message[0], 'Validation Error', 'error');
                        });
                    } else {
                        showSweetAlert('Server error.', 'Error', 'error');
                    }
                }
            });
        });

        // Validation & SweetAlert functions remain unchanged...
        function clearErrors() {
            $('.invalid-feedback').text('').hide();
            $('.is-invalid').removeClass('is-invalid');
        }

        function showError(field, message) {
            field.addClass('is-invalid');
            field.next('.invalid-feedback').text(message).show();
            field.focus();
        }

        function validateForm() {
            let isValid = true;
            clearErrors();

            const orderDate = $('input[name="order_date"]');
            if (!orderDate.val()) {
                showError(orderDate, 'Order date is required.');
                isValid = false;
            }

            // Dealer-specific validation
            if (type === 'dealer') {
                const qtyInput = $('input[name="orders[0][order_qty]"]');
                const qty = parseFloat(qtyInput.val());
                const maxLimit = parseFloat(qtyInput.data('max-limit')) || 0;

                if (isNaN(qty) || qty < 0.1) {
                    showError(qtyInput, 'Quantity must be ≥ 0.1 MT.');
                    isValid = false;
                } else if (qty > maxLimit) {
                    showError(qtyInput, `Exceeds allowed limit: ${maxLimit} MT`);
                    isValid = false;
                }
            }

            // Distributor: Validate each row
            let hasError = false;
            $('#dist_order_table tbody tr').each(function(index) {
                const row = $(this);
                const forType = row.find('[name$="[for_type]"]').val();
                const dealerSelect = row.find('[name$="[dealer_id]"]');
                const qtyInput = row.find('[name$="[order_qty]"]');
                const qty = parseFloat(qtyInput.val());
                let maxLimit = 0;

                // Determine max limit
                if (forType === 'self') {
                    maxLimit = selfAllowedLimit;
                } else if (forType === 'dealer') {
                    maxLimit = parseFloat(dealerSelect.find('option:selected').data('allowed-limit')) || 0;
                }

                // Validate For Type
                if (!forType) {
                    showError(row.find('[name$="[for_type]"]'), 'Select order for.');
                    isValid = false;
                    hasError = true;
                }

                // Validate Dealer if "Dealer" selected
                if (forType === 'dealer' && !dealerSelect.val()) {
                    showError(dealerSelect, 'Select a dealer.');
                    isValid = false;
                    hasError = true;
                }

                // Validate Quantity
                if (isNaN(qty) || qty <= 0) {
                    showError(qtyInput, 'Enter valid quantity.');
                    isValid = false;
                    hasError = true;
                } else if (qty < 0.001) {
                    showError(qtyInput, 'Quantity must be ≥ 0.001 MT.');
                    isValid = false;
                    hasError = true;
                } else if (maxLimit !== undefined && qty > maxLimit) {
                    const limitMsg = maxLimit === 0
                        ? 'Dealer has 0 MT allowed limit. Cannot place order.'
                        : `Max allowed: ${maxLimit} MT`;
                    showError(qtyInput, limitMsg);
                    isValid = false;
                    hasError = true;
                }
            });

            // Scroll to first error
            if (!isValid && !hasError) {
                // Fallback: if no inline error shown, show modal
                showSweetAlert('Please fix the errors in the form.', 'Validation Error', 'error');
            } else if (!isValid) {
                // Auto-scroll to first invalid field
                $('.is-invalid').first().focus();
            }

            return isValid;
        }

        function showSweetAlert(message, title = 'Alert', icon = 'info') {
            return Swal.fire({ title, text: message, icon, confirmButtonText: 'OK' });
        }

        // const successMessage = localStorage.getItem('successMessage');
        // if (successMessage) {
        //     showSweetAlert(successMessage, 'Success', 'success').then(() => localStorage.removeItem('successMessage'));
        // }

        @if (Session::has('error'))
            showSweetAlert("{{ Session::get('error') }}", 'Error', 'error');
        @endif
        @if (Session::has('success'))
            showSweetAlert("{{ Session::get('success') }}", 'Success', 'success');
        @endif
        @if ($errors->any())
            let msg = '<ul>';
            @foreach ($errors->all() as $error) msg += '<li>{{ $error }}</li>'; @endforeach
            msg += '</ul>';
            showSweetAlert(msg, 'Validation Errors', 'error');
        @endif
    });
</script>
@endpush

@push('styles')
<style>
    .basic-info-card { background: #fff; border-radius: 12px; transition: all 0.25s ease-in-out; }
    .basic-info-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); }
    .icon-circle { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5, #3b82f6); display: flex; align-items: center; justify-content: center; }
    .info-block { display: flex; flex-direction: column; padding: 12px; border: 1px solid #f1f1f1; border-radius: 8px; background: #fafafa; height: 100%; }
    .info-label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
    .info-value { font-size: 14px; font-weight: 500; color: #111827; }
    .invalid-feedback { display: none; color: #dc3545; font-size: 12px; margin-top: 4px; }
    .is-invalid { border-color: #dc3545 !important; }
    .note-editor.note-frame { border-radius: 0.375rem; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('orderForm');
    const submitBtn = form.querySelector('button[type="submit"]'); // The "Save" button
    const originalText = submitBtn.innerHTML.trim();

    form.addEventListener('submit', function (e) {
        // Prevent double submission
        if (submitBtn.disabled) {
            e.preventDefault();
            return;
        }

        // Disable button and show spinner
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Saving...
        `;

        // Optional: Re-enable if AJAX fails (error handler will run)
        const resetButton = () => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        };

        // Attach reset to error cases
        const originalErrorHandler = window.currentAjaxErrorHandler || $.ajaxSettings.error;
        $.ajaxSetup({
            error: function (xhr, status, err) {
                resetButton(); // Re-enable button if request fails
                if (originalErrorHandler) originalErrorHandler(xhr, status, err);
            }
        });

        // Also re-enable on page unload (in case of redirect delay)
        window.addEventListener('beforeunload', resetButton);
    });

    // Bonus: If success redirect happens fast, button stays in loading state — that's fine!
    // But if user comes back (browser back button), restore original state
    window.addEventListener('pageshow', function () {
        if (submitBtn.disabled) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endpush

@endsection
