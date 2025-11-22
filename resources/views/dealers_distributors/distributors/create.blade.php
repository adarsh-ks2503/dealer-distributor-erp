@extends('layouts.main')
@section('title', 'Create Distributor')
@section('content')

<main id="main" class="main">
    <div class="dashboard-header pagetitle">
        <h1>Distributors</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Distributors</a></li>
                <li class="breadcrumb-item active">New</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">New</h4>
                <a href="{{ route('distributors.index') }}" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back
                </a>
            </div>

            <form action="{{ route('distributors.store') }}" method="POST">
                @csrf

                {{-- Global error summary --}}
                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>There were some problems with your input:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                <div class="card-body mt-3">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Distributor Details</h5>
                    <div class="row g-4">

                        {{-- Distributor Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distributor Name: <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control custom-input @error('name') is-invalid @enderror"
                                   placeholder="Enter Distributor Name"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Distributor Code --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Distributor Code: <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="code"
                                   class="form-control custom-input @error('code') is-invalid @enderror"
                                   placeholder="Enter Code"
                                   value="{{ old('code') }}"
                                   required>
                            @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile No.: <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <img src="https://flagcdn.com/in.svg" alt="India" width="20"> +91
                                </span>
                                <input type="text"
                                       name="mobile_no"
                                       class="form-control custom-input @error('mobile_no') is-invalid @enderror"
                                       placeholder="10-digit Mobile Number"
                                       value="{{ old('mobile_no') }}"
                                       pattern="\d{10}"
                                       maxlength="10"
                                       minlength="10"
                                       inputmode="numeric"
                                       required>
                            </div>
                            @error('mobile_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email: <span class="text-danger">*</span></label>
                            <input required type="email"
                                   name="email"
                                   class="form-control custom-input @error('email') is-invalid @enderror"
                                   placeholder="Enter Email"
                                   value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- GST Number --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">GST Number:</label>
                            <input type="text"
                                   name="gst_num"
                                   class="form-control custom-input @error('gst_num') is-invalid @enderror"
                                   placeholder="Enter GST Number"
                                   value="{{ old('gst_num') }}">
                            @error('gst_num')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PAN Number --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">PAN Number:</label>
                            <input type="text"
                                   name="pan_num"
                                   class="form-control custom-input @error('pan_num') is-invalid @enderror"
                                   placeholder="Enter PAN Number"
                                   value="{{ old('pan_num') }}">
                            @error('pan_num')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Order Limit (MT) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Order Limit (MT): <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="order_limit"
                                   class="form-control custom-input @error('order_limit') is-invalid @enderror"
                                   placeholder="Enter Order Limit"
                                   value="{{ old('order_limit') }}"
                                   min="0"
                                   required>
                            @error('order_limit')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remark --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Remark:</label>
                            <input type="text"
                                   name="remarks"
                                   class="form-control custom-input @error('remarks') is-invalid @enderror"
                                   placeholder="Enter Remark"
                                   value="{{ old('remarks') }}">
                            @error('remarks')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Contact Person Details --}}
                <div class="card-body mt-3">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Contact Person Details</h5>
                    <div class="row g-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <table class="col-md-12 table" id="contact_person_table">
                                    <thead>
                                        <tr>
                                            <th><strong>Name</strong></th>
                                            <th><strong>Mobile Number</strong></th>
                                            <th><strong>Email</strong></th>
                                            <th class="table_heading_action"><strong>Action</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody id="img_table">
                                        {{-- Render old contact persons if any --}}
                                        @php
                                            $oldContacts = old('contact_person', []);
                                        @endphp

                                        @if(count($oldContacts) > 0)
                                            @foreach($oldContacts as $index => $contact)
                                                <tr>
                                                    {{-- Name --}}
                                                    <td>
                                                        <input type="text"
                                                               name="contact_person[{{ $index }}][name]"
                                                               class="form-control custom-input @error("contact_person.$index.name") is-invalid @enderror"
                                                               value="{{ $contact['name'] ?? '' }}"
                                                               placeholder="Enter Name">
                                                        @error("contact_person.$index.name")
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Mobile Number --}}
                                                    <td>
                                                        <input type="text"
                                                               name="contact_person[{{ $index }}][mobile_no]"
                                                               class="form-control custom-input @error("contact_person.$index.mobile_no") is-invalid @enderror"
                                                               value="{{ $contact['mobile_no'] ?? '' }}"
                                                               placeholder="10-digit Mobile Number"
                                                               pattern="\d{10}"
                                                               maxlength="10"
                                                               minlength="10"
                                                               inputmode="numeric"
                                                               >
                                                        @error("contact_person.$index.mobile_no")
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Email --}}
                                                    <td>
                                                        <input type="email"
                                                               name="contact_person[{{ $index }}][email]"
                                                               class="form-control custom-input @error("contact_person.$index.email") is-invalid @enderror"
                                                               value="{{ $contact['email'] ?? '' }}"
                                                               placeholder="Enter Email">
                                                        @error("contact_person.$index.email")
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Action buttons --}}
                                                    <td>
                                                        <button type="button" onclick="addContactRow(event)"
                                                                class="btn btn-success"><i class="fas fa-plus-circle"></i></button>
                                                        @if($index > 0)
                                                            <button type="button" onclick="deleteRow(this)"
                                                                    class="btn btn-danger"><i class="fas fa-minus-circle"></i></button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            {{-- No old contact person — show one empty row by default --}}
                                            <tr>
                                                <td>
                                                    <input type="text"
                                                           name="contact_person[0][name]"
                                                           class="form-control custom-input @error("contact_person.0.name") is-invalid @enderror"
                                                           value="{{ old('contact_person.0.name', '') }}"
                                                           placeholder="Enter Name">
                                                    @error("contact_person.0.name")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           name="contact_person[0][mobile_no]"
                                                           class="form-control custom-input @error("contact_person.0.mobile_no") is-invalid @enderror"
                                                           value="{{ old('contact_person.0.mobile_no', '') }}"
                                                           placeholder="10-digit Mobile Number"
                                                           pattern="\d{10}"
                                                           maxlength="10"
                                                           minlength="10"
                                                           inputmode="numeric"
                                                           >
                                                    @error("contact_person.0.mobile_no")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="email"
                                                           name="contact_person[0][email]"
                                                           class="form-control custom-input @error("contact_person.0.email") is-invalid @enderror"
                                                           value="{{ old('contact_person.0.email', '') }}"
                                                           placeholder="Enter Email">
                                                    @error("contact_person.0.email")
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <button type="button" onclick="addContactRow(event)"
                                                            class="btn btn-success"><i class="fas fa-plus-circle"></i></button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Distributor Address Details --}}
                <div class="card-body mt-3">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Distributor Address Details</h5>
                    <div class="row g-4">
                        {{-- Address --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address:</label>
                            <input type="text"
                                   name="address"
                                   class="form-control custom-input @error('address') is-invalid @enderror"
                                   placeholder="Enter Address"
                                   value="{{ old('address') }}">
                            @error('address')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pincode --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pincode:</label>
                            <input type="text"
                                    maxlength="6"
                                   name="pincode"
                                   class="form-control custom-input @error('pincode') is-invalid @enderror"
                                   placeholder="Enter Pincode"
                                   value="{{ old('pincode') }}">
                            @error('pincode')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- State --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">State: <span class="text-danger">*</span></label>
                            <select name="state_id"
                                    id="state"
                                    class="form-select form-select-sm @error('state_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Select State --</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                        {{ $state->state }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- City --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City: <span class="text-danger">*</span></label>
                            <select name="city_id"
                                    id="city"
                                    class="form-select form-select-sm @error('city_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Select City --</option>
                                {{-- If old city_id exists, you might load it via JS or pass to view --}}
                                @if(old('city_id'))
                                    <option value="{{ old('city_id') }}" selected>{{ old('city_name', '') }}</option>
                                @endif
                            </select>
                            @error('city_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Bank Account Details --}}
                <div class="card-body mt-3">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Bank Account Details</h5>
                    <div class="row g-4">

                        {{-- Bank Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bank Name: </label>
                            <input type="text"
                                   name="bank_name"
                                   class="form-control custom-input @error('bank_name') is-invalid @enderror"
                                   placeholder="Enter Bank Name"
                                   value="{{ old('bank_name') }}"
                                   >
                            @error('bank_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Account Holder Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account Holder Name:</label>
                            <input type="text"
                                   name="account_holder_name"
                                   class="form-control custom-input @error('account_holder_name') is-invalid @enderror"
                                   placeholder="Enter Holder Name"
                                   value="{{ old('account_holder_name') }}">
                            @error('account_holder_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- IFSC Code --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IFSC Code: </label>
                            <input type="text"
                                   name="ifsc_code"
                                   class="form-control custom-input @error('ifsc_code') is-invalid @enderror"
                                   placeholder="Enter IFSC Code"
                                   value="{{ old('ifsc_code') }}"
                                   >
                            @error('ifsc_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Account Number --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account Number:</label>
                            <input type="text"
                                   name="account_number"
                                   class="form-control custom-input @error('account_number') is-invalid @enderror"
                                   placeholder="Enter Account Number"
                                   value="{{ old('account_number') }}">
                            @error('account_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Submit Button --}}
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn custom-btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Submit
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </section>
</main>

@push('styles')
<style>
    .custom-input {
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-size: 14.5px;
    }
    .custom-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    .custom-btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
    }
    .custom-btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        transform: translateY(-2px);
    }
    .input-group-text {
        background-color: #f3f4f6;
        border: 2px solid #cbd5e1;
        border-right: none;
        font-weight: 500;
    }
    .input-group input.custom-input {
        border-left: none;
    }
    .card {
        border-radius: 12px;
        border: none;
    }
    .card-header {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .form-label {
        font-size: 14px;
        color: #374151;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let contactIndex = {{ count(old('contact_person', [])) > 0 ? count(old('contact_person')) : 1 }};

    function addContactRow(event) {
        event.preventDefault();
        const tableBody = document.getElementById('img_table');
        const index = contactIndex;

        const row = document.createElement('tr');

        // Name cell
        const tdName = document.createElement('td');
        tdName.innerHTML = `<input type="text" name="contact_person[${index}][name]" class="form-control custom-input" placeholder="Enter Name">`;
        row.appendChild(tdName);

        // Mobile cell
        const tdMobile = document.createElement('td');
        tdMobile.innerHTML = `<input type="text" name="contact_person[${index}][mobile_no]" class="form-control custom-input" placeholder="10-digit Mobile Number" pattern="\\d{10}" maxlength="10" minlength="10" inputmode="numeric">`;
        row.appendChild(tdMobile);

        // Email cell
        const tdEmail = document.createElement('td');
        tdEmail.innerHTML = `<input type="email" name="contact_person[${index}][email]" class="form-control custom-input" placeholder="Enter Email">`;
        row.appendChild(tdEmail);

        // Actions cell
        const tdAction = document.createElement('td');
        tdAction.innerHTML = `<button type="button" onclick="addContactRow(event)" class="btn btn-success"><i class="fas fa-plus-circle"></i></button>
                              <button type="button" onclick="deleteRow(this)" class="btn btn-danger"><i class="fas fa-minus-circle"></i></button>`;
        row.appendChild(tdAction);

        tableBody.appendChild(row);
        contactIndex++;
    }

    function deleteRow(button) {
        const row = button.closest('tr');
        row.remove();
    }

    $(document).ready(function() {
        $('#state').select2({
            placeholder: "-- Select State --",
            allowClear: true
        });

        $('#city').select2({
            placeholder: "-- Select City --",
            allowClear: true
        });
    });

    // State / City AJAX: (you already have this logic; ensure old city is set via JS or backend)
    $('#state').on('change', function() {
        var stateID = $(this).val();
        $('#city').html('<option value="">-- Select City --</option>');
        if (stateID) {
            $.ajax({
                url: '/get-cities/' + stateID,
                type: 'GET',
                success: function(data) {
                    $('#city').html('<option value="">-- Select City --</option>');
                    $.each(data, function(key, city) {
                        $('#city').append('<option value="'+ city.id +'">'+ city.name +'</option>');
                    });
                    $('#city').trigger('change');
                    // if old('city_id') exists, select it
                    @if(old('city_id'))
                        $('#city').val('{{ old('city_id') }}').trigger('change');
                    @endif
                }
            });
        } else {
            $('#city').html('<option value="">-- Select City --</option>').trigger('change');
        }
    });

    // On page load, if old state exists, trigger change to load old cities
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('state_id'))
            $('#state').val('{{ old('state_id') }}').trigger('change');
        @endif
    });
</script>
@endpush

@endsection
