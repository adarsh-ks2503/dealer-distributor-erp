@extends('layouts.main')
@section('title', ' Company Details- Singhal Steel')
@section('content')
    <main id="main" class="main">
        @if ($message = Session::get('update'))
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

        @if ($errors->any())
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-xmark-circle error"></i>
                    <div class="message">
                        <span class="text text-1">Error</span>
                        <span class="text text-2">Company Setting Update UnSuccessfully</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif
        <div class="dashboard-header pagetitle">
            <h1>Company Details</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Company Details</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <form class="row g-3" method="post" action="{{ route('setting.company_update') }}"
                onsubmit="return validateForm()" id="noteForm_2">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mt-3">
                            <div class="card-body ">
                                <h5 class="card-title">Company Details</h5>
                                <div class="row pt-4">
                                    <div class="col-md-6">
                                        <label for="inputName5" class="form-label">Company Name</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="name" class="form-control"
                                            value="{{ $data->name }}" required readonly>
                                        @if ($errors->has('name'))
                                            <p class="error">{{ $errors->first('name') }}</p>
                                        @endif
                                    </div><br>

                                    <div class="col-md-6">
                                        <label for="inputName5" class="form-label">Email </label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="email" class="form-control"
                                            value="{{ $data->email }}" required readonly>
                                        @if ($errors->has('email'))
                                            <p class="error">{{ $errors->first('email') }}</p>
                                        @endif
                                    </div>


                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Phone Number <span
                                                class="required-classes">*</span></label>
                                        <input type="number" id="edit-input" name="phone_number" class="form-control"
                                            value="{{ $data->phone_number }}" required readonly
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10);">
                                        @if ($errors->has('phone_number'))
                                            <p class="error">{{ $errors->first('phone_number') }}</p>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">City</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="city" class="form-control"
                                            value="{{ $data->city }}" required readonly>

                                        @if ($errors->has('city'))
                                            <p class="error">{{ $errors->first('city') }}</p>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">State</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="state" class="form-control"
                                            value="{{ $data->state }}" required readonly>
                                        @if ($errors->has('state'))
                                            <p class="error">{{ $errors->first('state') }}</p>
                                        @endif
                                    </div>



                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Country</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="country" class="form-control"
                                            value="{{ $data->country }}" required readonly>
                                        @if ($errors->has('country'))
                                            <p class="error">{{ $errors->first('country') }}</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Pincode</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="pincode" class="form-control"
                                            value="{{ $data->pincode }}" required maxlength="6" readonly>
                                        @if ($errors->has('pincode'))
                                            <p class="error">{{ $errors->first('pincode') }}</p>
                                        @endif
                                    </div>



                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Address</label>
                                        <input type="text" id="edit-input" name="address" class="form-control"
                                            value="{{ $data->address }}" required readonly>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">GST Number</label>
                                        <input type="text" id="edit-input" name="gst_no" class="form-control"
                                            value="{{ $data->gst_no }}" required readonly minlength="15" maxlength="15">

                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Pan Number</label>
                                        <input type="text" id="edit-input" name="pan" class="form-control"
                                            value="{{ $data->pan }}" required readonly maxlength="10">
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Tan Number</label>
                                        <input type="text" id="edit-input" name="tan" class="form-control"
                                            value="{{ $data->tan }}" required readonly maxlength="10">

                                    </div>

                                    {{-- <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Threshold %</label>
                                        <input type="text" id="edit-input" name="threshold" class="form-control"
                                            value="{{ $data->threshold }}" required readonly
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">


                                    </div> --}}
                                </div>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Account Details</h5>
                                <div class="row">
                                    {{-- <div class="col-md-6">
                                        <label for="inputName5" class="form-label">Amount</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="amount" class="form-control"
                                            value="{{ $data->amount }}" required readonly maxlength="18">
                                        @if ($errors->has('amount'))
                                            <p class="error">{{ $errors->first('amount') }}</p>
                                        @endif
                                    </div> --}}
                                    <div class="col-md-6">
                                        <label for="inputName5" class="form-label">Account Number</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="ac_number" class="form-control"
                                            value="{{ $data->ac_number }}" required readonly maxlength="18">
                                        @if ($errors->has('ac_number'))
                                            <p class="error">{{ $errors->first('ac_number') }}</p>
                                        @endif
                                    </div>


                                    <div class="col-md-6 ">
                                        <label for="inputName5" class="form-label">IFSC Code</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="ifsc_code" class="form-control"
                                            value="{{ $data->ifsc_code }}" required readonly maxlength="15">
                                        @if ($errors->has('ifsc_code'))
                                            <p class="error">{{ $errors->first('ifsc_code') }}</p>
                                        @endif
                                    </div>


                                    <div class="col-md-6 mt-4">
                                        <label for="inputName5" class="form-label">Bank Name</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="bank_name" class="form-control"
                                            value="{{ $data->bank_name }}" required readonly>
                                        @if ($errors->has('bank_name'))
                                            <p class="error">{{ $errors->first('bank_name') }}</p>
                                        @endif
                                    </div>


                                    <div class="col-md-6  mt-4">
                                        <label for="inputName5" class="form-label">Branch Name</label><span
                                            class="required-classes">*</span>
                                        <input type="text" id="edit-input" name="branch" class="form-control"
                                            value="{{ $data->branch }}" required readonly>
                                        @if ($errors->has('branch'))
                                            <p class="error">{{ $errors->first('branch') }}</p>
                                        @endif
                                    </div>
                                    <div class="text-end mt-3">
                                        @can('Company-Update')
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        @endcan
                                        <a class="btn btn-secondary" href="/dashboard">Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
            </div>
        </section>
    </main><!-- End #main -->
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#edit-input').forEach(function(input) {
            input.style.backgroundColor = '#e9ecef';
            input.addEventListener('click', function() {
                this.removeAttribute('readonly');
                this.style.backgroundColor = 'white';
            });

            input.addEventListener('blur', function() {
                this.setAttribute('readonly', '');
                this.style.backgroundColor = '#e9ecef';


            });
        });
    });
</script>



<script>
    function validateForm() {
        var gstNo = document.getElementById("gst_no").value;
        var pan = document.getElementById("pan").value;
        var tan = document.getElementById("tan").value;

        if (gstNo === pan || gstNo === tan || pan === tan) {
            document.getElementById("error-message").style.display = "block";
            return false;
        } else {
            return true;
        }
    }
</script>
