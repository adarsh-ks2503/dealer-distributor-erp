@extends('layouts.main')
@section('title', ' Users Update- Singhal Steel')
@section('content')
    <main id="main" class="main">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                {{ $message }}
            </div>
        @endif
        <div class="dashboard-header pagetitle">
            <h1>Update User Management</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}"> User Management</a></li>

                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card pt-4">
                        <div class="card-body">
                            <h4 class="card-title">Update User Management</h4>

                            <form class="row g-3" action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <label for="name"><strong>First Name <span
                                                class="required-classes">*</span></strong></label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}"
                                        required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="last_name"><strong>Last Name <span
                                                class="required-classes">*</span></strong></label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="{{ $user->last_name }}" required>
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="mobile"><strong>Mobile <span
                                                class="required-classes">*</span></strong></label>
                                    <input type="number" name="mobile" class="form-control" value="{{ $user->mobile }}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email"><strong>Email <span
                                                class="required-classes">*</span></strong></label>
                                    <input type="text" name="email" class="form-control" value="{{ $user->email }}"
                                        required>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>




                                <div class="col-md-6">
                                    <label for="roles[]"><strong>Role <span
                                                class="required-classes">*</span></strong></label>
                                    <select name="roles[]" class="form-control" id="role__" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ in_array($role->id, $userRoles) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('roles[]')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password"><strong>Password</strong></label>
                                    <label for="password">The password field must be at least 8 characters.</label>

                                    <input type="password" name="password" class="form-control">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="confirm-password"><strong>Confirm Password</strong></label>
                                    <input type="password" name="confirm-password" class="form-control">
                                    @error('confirm-password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="text-end">
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
    <script>
        $(document).ready(function() {
            $('#role__').select2({});
        });
    </script>
@endsection
