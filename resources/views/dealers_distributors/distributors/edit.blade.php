@extends('layouts.main')
@section('title', 'Edit Distributor')
@section('content')

<main id="main" class="main">
    <div class="dashboard-header pagetitle">
        <h1>Distributors</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Distributors</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">Edit Distributor</h4>
                <a href="{{ route('distributors.index') }}" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back
                </a>
            </div>

            <form action="{{ route('distributors.update', $distributor->id) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
                                   value="{{ old('name', $distributor->name) }}"
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
                                   value="{{ old('code', $distributor->code) }}"
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
                                       value="{{ old('mobile_no', $distributor->mobile_no) }}"
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
                            <label class="form-label fw-semibold">Email:</label>
                            <input type="email"
                                   name="email"
                                   class="form-control custom-input @error('email') is-invalid @enderror"
                                   placeholder="Enter Email"
                                   value="{{ old('email', $distributor->email) }}">
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
                                   value="{{ old('gst_num', $distributor->gst_num) }}">
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
                                   value="{{ old('pan_num', $distributor->pan_num) }}">
                            @error('pan_num')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Order Limit (MT) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Order Limit (MT): <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="order_limit"
                                   class="form-control custom-input @error('order_limit') is-invalid @enderror"
                                   placeholder="Enter Order Limit"
                                   value="{{ old('order_limit', ($distributor->order_limit)) }}"
                                   readonly>
                            @error('order_limit')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remark --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Remark:</label>
                            <textarea name="remarks" id="" cols="30" rows="2" class="form-control @error('remarks') is-invalid @enderror" >{{ old('remarks', $distributor->remarks) }}</textarea>
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
                                            $contacts = old('contact_person') ?? $distributor->contactPersons ?? [];
                                        @endphp

                                        @if(count($contacts) > 0)
                                            @foreach($contacts as $index => $contact)
                                                <tr>
                                                    {{-- Name --}}
                                                    <td>
                                                        <input type="text"
                                                            name="contact_person[{{ $index }}][name]"
                                                            class="form-control custom-input @error("contact_person.$index.name") is-invalid @enderror"
                                                            value="{{ old("contact_person.$index.name", $contact->name ?? '') }}"
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
                                                            value="{{ old("contact_person.$index.mobile_no", $contact->mobile_no ?? '') }}"
                                                            placeholder="10-digit Mobile Number"
                                                            pattern="\d{10}"
                                                            maxlength="10"
                                                            minlength="10"
                                                            inputmode="numeric">
                                                        @error("contact_person.$index.mobile_no")
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Email --}}
                                                    <td>
                                                        <input type="email"
                                                            name="contact_person[{{ $index }}][email]"
                                                            class="form-control custom-input @error("contact_person.$index.email") is-invalid @enderror"
                                                            value="{{ old("contact_person.$index.email", $contact->email ?? '') }}"
                                                            placeholder="Enter Email">
                                                        @error("contact_person.$index.email")
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Action --}}
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
                                            {{-- No contacts at all - show one empty row --}}
                                            <tr>
                                                <td>
                                                    <input type="text"
                                                        name="contact_person[0][name]"
                                                        class="form-control custom-input @error('contact_person.0.name') is-invalid @enderror"
                                                        value="{{ old('contact_person.0.name') }}"
                                                        placeholder="Enter Name">
                                                    @error('contact_person.0.name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="contact_person[0][mobile_no]"
                                                        class="form-control custom-input @error('contact_person.0.mobile_no') is-invalid @enderror"
                                                        value="{{ old('contact_person.0.mobile_no') }}"
                                                        placeholder="10-digit Mobile Number"
                                                        pattern="\d{10}"
                                                        maxlength="10"
                                                        minlength="10"
                                                        inputmode="numeric">
                                                    @error('contact_person.0.mobile_no')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="email"
                                                        name="contact_person[0][email]"
                                                        class="form-control custom-input @error('contact_person.0.email') is-invalid @enderror"
                                                        value="{{ old('contact_person.0.email') }}"
                                                        placeholder="Enter Email">
                                                    @error('contact_person.0.email')
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

                {{-- Address Details --}}
                <div class="card-body mt-3">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Distributor Address Details</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address:</label>
                            <input type="text"
                                   name="address"
                                   class="form-control custom-input @error('address') is-invalid @enderror"
                                   placeholder="Enter Address"
                                   value="{{ old('address', $distributor->address) }}">
                            @error('address')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pincode:</label>
                            <input type="text"
                                   name="pincode"
                                   maxlength="6"
                                   class="form-control custom-input @error('pincode') is-invalid @enderror"
                                   placeholder="Enter Pincode"
                                   value="{{ old('pincode', $distributor->pincode) }}">
                            @error('pincode')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">State: <span class="text-danger">*</span></label>
                            <div class="readonly-field p-2 border rounded bg-light">
                                {{ $distributor->state?->state ?? 'N/A' }}
                            </div>
                            <input type="hidden" name="state_id" value="{{ old('state_id', $distributor->state_id) }}">
                            @error('state_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City: <span class="text-danger">*</span></label>
                            <select name="city_id"
                                    id="city"
                                    class="form-select form-select-sm @error('city_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Select City --</option>
                                @if(old('city_id', $distributor->city_id))
                                    <option value="{{ old('city_id', $distributor->city_id) }}" selected>
                                        {{ $distributor->city?->name ?? '' }}
                                    </option>
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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bank Name: </label>
                            <input type="text"
                                   name="bank_name"
                                   class="form-control custom-input @error('bank_name') is-invalid @enderror"
                                   placeholder="Enter Bank Name"
                                   value="{{ old('bank_name', $distributor->bank_name) }}">
                            @error('bank_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account Holder Name:</label>
                            <input type="text"
                                   name="account_holder_name"
                                   class="form-control custom-input @error('account_holder_name') is-invalid @enderror"
                                   placeholder="Enter Holder Name"
                                   value="{{ old('account_holder_name', $distributor->account_holder_name) }}">
                            @error('account_holder_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IFSC Code: </label>
                            <input type="text"
                                   name="ifsc_code"
                                   class="form-control custom-input @error('ifsc_code') is-invalid @enderror"
                                   placeholder="Enter IFSC Code"
                                   value="{{ old('ifsc_code', $distributor->ifsc_code) }}">
                            @error('ifsc_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account Number:</label>
                            <input type="text"
                                   name="account_number"
                                   class="form-control custom-input @error('account_number') is-invalid @enderror"
                                   placeholder="Enter Account Number"
                                   value="{{ old('account_number', $distributor->account_number) }}">
                            @error('account_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="col-12 text-end mt-4 mb-3">
                        <button type="submit" class="btn custom-btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Update
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </section>
</main>

@push('styles')
<style>
    .custom-input, .form-select {
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-size: 14.5px;
    }
    .custom-input:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    .custom-btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
    }
    .custom-btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        transform: translateY(-2px);
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .is-invalid + .select2-container .select2-selection {
        border-color: #dc3545 !important;
    }
    .select2-container .select2-selection--single {
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        height: 38px;
        font-size: 14.5px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    .select2-dropdown {
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14.5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for state and city dropdowns
        $('#state').select2({
            placeholder: '-- Select State --',
            allowClear: true,
            width: '100%'
        });

        $('#city').select2({
            placeholder: '-- Select City --',
            allowClear: true,
            width: '100%'
        });

        // State change handler for cascading cities
        $('#state').on('change', function() {
            var stateId = $(this).val();
            $('#city').html('<option value="">Loading...</option>').trigger('change');
            if (stateId) {
                $.get('/get-cities/' + stateId, function(data) {
                    $('#city').empty().append('<option value="">-- Select City --</option>');
                    $.each(data, function(index, city) {
                        $('#city').append('<option value="' + city.id + '">' + city.name + '</option>');
                    });
                    @if(old('city_id', $distributor->city_id))
                        $('#city').val('{{ old('city_id', $distributor->city_id) }}').trigger('change');
                    @endif
                }).fail(function() {
                    $('#city').html('<option value="">Error loading cities</option>').trigger('change');
                });
            } else {
                $('#city').html('<option value="">-- Select City --</option>').trigger('change');
            }
        });

        // Trigger state change on page load to populate cities
        @if(old('state_id', $distributor->state_id))
            $('#state').val('{{ old('state_id', $distributor->state_id) }}').trigger('change');
        @endif

        // Handle Select2 validation for required fields
        $('form').on('submit', function() {
            if (!$('#state').val()) {
                $('#state').closest('.select2-container').addClass('is-invalid');
            } else {
                $('#state').closest('.select2-container').removeClass('is-invalid');
            }
            if (!$('#city').val()) {
                $('#city').closest('.select2-container').addClass('is-invalid');
            } else {
                $('#city').closest('.select2-container').removeClass('is-invalid');
            }
        });

        // Remove is-invalid class when a value is selected
        $('#state, #city').on('change', function() {
            if ($(this).val()) {
                $(this).closest('.select2-container').removeClass('is-invalid');
            }
        });
    });

    function addContactRow(event) {
        event.preventDefault();
        const tableBody = document.getElementById('img_table');
        const rowCount = tableBody.getElementsByTagName('tr').length;
        const newRow = createContactRow(rowCount);
        tableBody.appendChild(newRow);
    }

    function createContactRow(index, contact = {}) {
        const row = document.createElement('tr');
        const nameValue = contact.name || '';
        const mobileValue = contact.mobile_no || '';
        const emailValue = contact.email || '';

        row.innerHTML = `
            <td>
                <input type="text"
                       name="contact_person[${index}][name]"
                       class="form-control custom-input"
                       placeholder="Enter Name"
                       value="${nameValue}">
            </td>
            <td>
                <input type="text"
                       name="contact_person[${index}][mobile_no]"
                       class="form-control custom-input"
                       placeholder="10-digit Mobile Number"
                       pattern="\\d{10}"
                       maxlength="10"
                       minlength="10"
                       inputmode="numeric"
                       value="${mobileValue}">
            </td>
            <td>
                <input type="email"
                       name="contact_person[${index}][email]"
                       class="form-control custom-input"
                       placeholder="Enter Email"
                       value="${emailValue}">
            </td>
            <td>
                <button type="button" onclick="addContactRow(event)" class="btn btn-success"><i class="fas fa-plus-circle"></i></button>
                <button type="button" onclick="deleteRow(this)" class="btn btn-danger"><i class="fas fa-minus-circle"></i></button>
            </td>
        `;
        return row;
    }

    function deleteRow(button) {
        const tableBody = document.getElementById('img_table');
        const row = button.closest('tr');
        if (tableBody.rows.length > 1) { // Prevents deleting the last row
            row.remove();
            reindexContactRows();
        }
    }

    function reindexContactRows() {
        const tableBody = document.getElementById('img_table');
        const rows = tableBody.getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            const inputs = rows[i].querySelectorAll('input');
            inputs.forEach(input => {
                const nameAttribute = input.getAttribute('name');
                if (nameAttribute) {
                    const newName = nameAttribute.replace(/\[\d+\]/, `[${i}]`);
                    input.setAttribute('name', newName);
                }
            });
        }
    }

    // Initial population of contact persons
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('img_table');
        tableBody.innerHTML = '';
        const oldContacts = @json(old('contact_person', $distributor->contactPersons));

        if (oldContacts && oldContacts.length > 0) {
            oldContacts.forEach((contact, index) => {
                const row = createContactRow(index, contact);
                tableBody.appendChild(row);
            });
        } else {
            const newRow = createContactRow(0);
            tableBody.appendChild(newRow);
        }
    });
</script>
@endpush

@endsection
