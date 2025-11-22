@extends('layouts.main')
@section('title', 'Edit App User - Singhal Steel')
@section('content')
    <main id="main" class="main">
        @if ($message = Session::get('success'))
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-check check"></i>
                    <div class="message">
                        <span class="text text-1">Success</span>
                        <span class="text text-2">{{ $message }}</span>
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
                        <span class="text text-2">{{ implode('', $errors->all(':message')) }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        <div class="dashboard-header pagetitle">
            <h1>Edit App User</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('appUserManagement') }}">App User Management</a></li>
                    <li class="breadcrumb-item active">Edit User</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <form action="{{ route('app-users.update', $user->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">User Details</h5>
                                <div class="row pt-4">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Name <span class="required-classes">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required readonly>
                                        @error('name')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="type" class="form-label">Type <span class="required-classes">*</span></label>
                                        <input type="text" name="type" id="type" class="form-control" value="{{ old('type', $user->type) }}" required readonly>
                                        @error('type')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="code" class="form-label">Code <span class="required-classes">*</span></label>
                                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $user->code) }}" required readonly>
                                        @error('code')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="email" class="form-label">Email <span class="required-classes">*</span></label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required readonly>
                                        @error('email')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="mobile_no" class="form-label">Mobile No <span class="required-classes">*</span></label>
                                        <input type="text" name="mobile_no" id="mobile_no" class="form-control" value="{{ old('mobile_no', $user->mobile_no) }}" required readonly maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10);">
                                        @error('mobile_no')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="password" class="form-label">New Password (Leave blank to keep current)<span class="required-classes">*</span></label>
                                        <input required type="password" name="password" id="password" class="form-control" placeholder="Enter new password">
                                        @error('password')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <label for="password_confirmation" class="form-label">Confirm New Password <span class="required-classes">*</span></label>
                                        <input required type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm new password">
                                        @error('password_confirmation')
                                            <p class="error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('appUserManagement') }}" class="btn btn-secondary ms-2">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main><!-- End #main -->
@endsection
