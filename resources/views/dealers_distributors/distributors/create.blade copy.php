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
                <div class="card-body mt-3">
                        <h5 class="fw-bold mb-4 border-bottom pb-2">Distributor Details</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Distributor Name: <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control custom-input" placeholder="Enter" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Distributor Code:</label>
                                <input type="text" name="code" class="form-control custom-input" placeholder="Enter">
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mobile No. No.: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><img src="https://flagcdn.com/in.svg" alt="India" width="20"> +91</span>
                                    <input pattern="\d{10}" type="text" name="mobile_no" class="form-control custom-input" placeholder="Mobile No. Number" required>
                                    @error('mobile_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email:</label>
                                <input type="email" name="email" class="form-control custom-input" placeholder="Enter">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">GST Number:</label>
                                <input type="text" name="gst_num" class="form-control custom-input" placeholder="Enter">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">PAN Number:</label>
                                <input type="text" name="pan_num" class="form-control custom-input" placeholder="Enter">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Order Limit (MT): <span class="text-danger">*</span></label>
                                <input type="number" name="order_limit" class="form-control custom-input" placeholder="By default (value = 0)" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Remark:</label>
                                <input type="text" name="remarks" class="form-control custom-input" placeholder="Enter">
                            </div>

                        </div>
                </div>

                <div class="card-body mt-3">
                        <h5 class="fw-bold mb-4 border-bottom pb-2">Contact Person Details</h5>
                        <div class="row g-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <table class="col-md-12 table">
                                        <thead>
                                            <tr>
                                                <th ><strong>Name </strong></th>
                                                <th ><strong>Mobile Number </strong></th>
                                                <th ><strong>Email </strong></th>
                                                <th class="table_heading_action"><strong>Action</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody id="img_table">
                                            <tr></tr>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                </div>

                <div class="card-body mt-3">
                        <h5 class="fw-bold mb-4 border-bottom pb-2">Distributor Address Details</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address: </label>
                                <input type="text" name="address" class="form-control custom-input" placeholder="Enter">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pincode:</label>
                                <input type="number" name="pincode" class="form-control custom-input" placeholder="Enter">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">State: <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" aria-label="Small select example" name="state_id" id="state">
                                    <option value="">-- Select State --</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City: <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" aria-label="Small select example" name="city_id" id="city">
                                    <option value="">-- Select City --</option>
                                </select>
                            </div>

                        </div>
                </div>

                <div class="card-body mt-3">
                        <h5 class="fw-bold mb-4 border-bottom pb-2">Bank Account Details</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bank Name: <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" class="form-control custom-input" placeholder="Enter" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account Holder Name:</label>
                                <input type="text" name="account_holder_name" class="form-control custom-input" placeholder="Enter">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">IFSC Code: <span class="text-danger">*</span></label>
                                <input type="text" name="ifsc_code" class="form-control custom-input" placeholder="IFSC Code" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account Number:</label>
                                <input type="number" name="account_number" class="form-control custom-input" placeholder="Enter">
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn custom-btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Submit
                                </button>
                            </div>

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
        </style>
    @endpush

    <script>
            var index = 1; // Initial Item ID

            function attachment_add_Row_1(event) {
                event.preventDefault();
                var table = document.getElementById("img_table");
                var newRow = table.insertRow(table.rows.length);

                var cell1 = newRow.insertCell(0);
                var cell2 = newRow.insertCell(1);
                var cell3 = newRow.insertCell(2);
                var cell4 = newRow.insertCell(3);
                var cell5 = newRow.insertCell(4);


                cell1.innerHTML =
                    `<input type="text" name="contact_person[${index}][name]" class="form-control">`;
                
                cell2.innerHTML =
                    `<input type="number" pattern="\d{10}" name="contact_person[${index}][mobile_no]" class="form-control">`;

                cell3.innerHTML =
                    `<input type="email" name="contact_person[${index}][email]" class="form-control" placeholder="Enter email"/>`;
                cell4.innerHTML =
                    `<button onclick="attachment_add_Row_1(event)" class="btn btn-success"><i class="fas fa-plus-circle"></i></button>`;
                if (index != 1) {
                    cell5.innerHTML =
                        `<button class="btn btn-danger" onclick="deleteRow(this)"><i class="fas fa-minus-circle"></i></button>`;
                }


                index++;

            }

            function initializeTable_2() {
                attachment_add_Row_1(event);
            }
            document.addEventListener('DOMContentLoaded', initializeTable_2);

            function deleteRow(button) {
                var row = button.parentNode.parentNode;
                var table = document.getElementById("myTable");
                var rowIndex = row.rowIndex;

                row.parentNode.removeChild(row);
                attachment_lastItemId--;
            }
        </script>

    <script>
        $('#state').on('change', function() {
            var stateID = $(this).val();
            $('#city').html('<option>Loading...</option>');

            if (stateID) {
                $.ajax({
                    url: '/get-cities/' + stateID,
                    type: 'GET',
                    success: function(data) {
                        $('#city').empty().append('<option value="">-- Select City --</option>');
                        $.each(data, function(key, city) {
                            $('#city').append('<option value="'+ city.id +'">'+ city.name +'</option>');
                        });
                    }
                });
            } else {
                $('#city').html('<option value="">-- Select City --</option>');
            }
        });
    </script>

@endsection
