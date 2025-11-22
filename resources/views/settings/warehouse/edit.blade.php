@extends('layouts.main')
@section('title', 'Warehouse Management - Edit Warehouse')
@section('content')

<main id="main" class="main">
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

    @if ($message = Session::get('error'))
        <div class="tt active">
            <div class="tt-content">
                <i class="fas fa-solid fa-xmark-circle error"></i>
                <div class="message">
                    <span class="text text-1">Error</span>
                    <span class="text text-2"> {{ $message }}</span>
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="pg active"></div>
        </div>
    @endif

    @if ($errors->any())
        <div class="tt active">
            <div class="tt-content">
                <i class="fas fa-solid fa-xmark-circle error"></i>
                <div class="message">
                    <span class="text text-1">Error</span>
                    <span class="text text-2">Please fix the errors below.</span>
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="pg active"></div>
        </div>
    @endif

    <div class="dashboard-header pagetitle">
        <h1>Edit Warehouse</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Settings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('warehouse.index') }}">Warehouse</a></li>
                <li class="breadcrumb-item active">Edit Warehouse</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-2">
                    <div class="card-body mt-4">
                        <form action="{{ route('warehouse.update', $warehouse->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Warehouse Name -->
                            <div class="form-group mb-3">
                                <label for="warehouse_name">Warehouse Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                                    <input type="text" id="warehouse_name" name="warehouse_name" class="form-control"
                                        placeholder="Enter Warehouse Name"
                                        value="{{ old('warehouse_name', $warehouse->name) }}" required>
                                </div>
                                @error('warehouse_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mobile -->
                            <div class="form-group mb-3">
                                <label for="mobile">Mobile No. <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                    <input type="text"
                                        id="mobile"
                                        name="mobile"
                                        class="form-control"
                                        placeholder="Enter 10-digit Mobile No"
                                        maxlength="10"
                                        value="{{ old('mobile', $warehouse->mobile_no === 'null' ? '' : $warehouse->mobile_no) }}"
                                        required>
                                </div>
                                <div class="invalid-feedback d-block" id="mobile-error"></div>
                            </div>

                            <!-- PAN -->
                            <div class="form-group mb-3">
                                <label for="pan">PAN</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" id="pan" name="pan" class="form-control"
                                        placeholder="Enter PAN"
                                        value="{{ old('pan', $warehouse->pan_no) }}">
                                </div>
                            </div>

                            <!-- GST -->
                            <div class="form-group mb-3">
                                <label for="gst_no">GST No.</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                    <input type="text" id="gst_no" name="gst_no" class="form-control"
                                        placeholder="Enter GST No."
                                        value="{{ old('gst_no', $warehouse->gst_no) }}">
                                </div>
                            </div>

                            <!-- State (Searchable) -->
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">State: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    {{-- <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span> --}}
                                    <select name="state_id"
                                        id="state"
                                        class="form-select @error('state_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Select State --</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ old('state_id', $warehouse->state_id) == $state->id ? 'selected' : '' }}>
                                                {{ $state->state }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('state_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- City (Searchable + AJAX) -->
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">City: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    {{-- <span class="input-group-text"><i class="fas fa-city"></i></span> --}}
                                    <select name="city_id"
                                        id="city"
                                        class="form-select @error('city_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Select City --</option>
                                    </select>
                                </div>
                                @error('city_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pincode -->
                            <div class="form-group mb-3">
                                <label for="pincode">Pincode <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                    <input type="text"
                                        id="pincode"
                                        name="pincode"
                                        class="form-control"
                                        placeholder="Enter 6-digit Pincode"
                                        maxlength="6"
                                        value="{{ old('pincode', $warehouse->pincode) }}"
                                        required>
                                </div>
                                <div class="invalid-feedback d-block" id="pincode-error"></div>
                            </div>

                            <!-- Address -->
                            <div class="form-group mb-3">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <textarea id="address" name="address" class="form-control" placeholder="Enter Address" required>{{ old('address', $warehouse->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('warehouse.index') }}'">Cancel</button>
                                <button type="submit" class="btn custom-btn-primary ms-3">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .form-control {
        border-radius: 6px;
        padding: 12px 15px;
        font-size: 14px;
        transition: border 0.3s ease;
    }

    .form-control:focus {
        border: 2px solid #3e5bb0;
        box-shadow: 0 0 8px rgba(62, 91, 176, 0.5);
    }

    .input-group-text {
        background-color: #5c6bc0;
        color: white;
    }

    .custom-btn-primary {
        background: linear-gradient(135deg, #5c6bc0, #3e5bb0);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease-in-out;
    }

    .custom-btn-primary:hover {
        background: linear-gradient(135deg, #3e5bb0, #607d8b);
        transform: translateY(-2px);
        color: #fff;
    }

    .breadcrumb-item a {
        color: #4e73df;
        font-weight: 600;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .btn {
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 6px;
        height: 48px;
        padding: 0.375rem 0.75rem;
        font-size: 14px;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 8px;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 6px;
    }

    .is-invalid ~ .select2-container .select2-selection {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 0.25rem;
        display: block;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // === Input Restrictions ===
    $('#mobile, #pincode').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#mobile').on('blur', function () {
        const val = $(this).val();
        const errorDiv = $('#mobile-error');
        if (val && val.length !== 10) {
            errorDiv.text('Mobile number must be exactly 10 digits.');
            $(this).addClass('is-invalid');
        } else {
            errorDiv.text('');
            $(this).removeClass('is-invalid');
        }
    });

    $('#pincode').on('blur', function () {
        const val = $(this).val();
        const errorDiv = $('#pincode-error');
        if (val && val.length !== 6) {
            errorDiv.text('Pincode must be exactly 6 digits.');
            $(this).addClass('is-invalid');
        } else {
            errorDiv.text('');
            $(this).removeClass('is-invalid');
        }
    });

    // === Form Submit Validation ===
    $('form').on('submit', function (e) {
        let isValid = true;

        const mobile = $('#mobile').val().trim();
        if (mobile.length !== 10) {
            $('#mobile-error').text('Mobile number must be exactly 10 digits.');
            $('#mobile').addClass('is-invalid');
            isValid = false;
        }

        const pincode = $('#pincode').val().trim();
        if (pincode.length !== 6) {
            $('#pincode-error').text('Pincode must be exactly 6 digits.');
            $('#pincode').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 120
            }, 500);
        }
    });

    // === Select2 Initialization ===
    $('#state').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Select State --',
        allowClear: true,
        width: '100%'
    });

    $('#city').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Select City --',
        allowClear: true,
        width: '100%',
        ajax: {
            url: function() {
                const stateId = $('#state').val();
                return stateId ? `/get-cities/${stateId}` : null;
            },
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(city => ({
                        id: city.id,
                        text: city.name
                    }))
                };
            },
            cache: true
        }
    });

    // === Load Cities on State Change ===
    $('#state').on('change', function() {
        $('#city').empty().trigger('change');
    });

    // === Initialize with Existing Data ===
    const currentStateId = '{{ old('state_id', $warehouse->state_id) }}';
    const currentCityId = '{{ old('city_id', $warehouse->city_id) }}';

    if (currentStateId) {
        $('#state').val(currentStateId).trigger('change');

        // Load cities for current state and pre-select city
        $.get('/get-cities/' + currentStateId, function(data) {
            data.forEach(city => {
                const selected = city.id == currentCityId;
                const option = new Option(city.name, city.id, selected, selected);
                $('#city').append(option);
            });
            $('#city').trigger('change');
        }).fail(function() {
            $('#city').append('<option value="">-- Error loading cities --</option>');
        });
    }

    // === Close Toast Alerts ===
    $('.tt .close').on('click', function() {
        $(this).parent('.tt').removeClass('active');
    });
});
</script>
@endpush
