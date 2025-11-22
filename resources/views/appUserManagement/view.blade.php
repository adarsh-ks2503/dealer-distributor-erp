@extends('layouts.main')
@section('title', 'View App User - Singhal Steel')
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
                        <span class="text text-2">An error occurred</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        <div class="dashboard-header pagetitle">
            <h1>View App User</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('appUserManagement') }}">App User Management</a></li>
                    <li class="breadcrumb-item active">View User</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> {{ $user->name }}</p>
                                    <p><strong>Type:</strong> {{ $user->type }}</p>
                                    <p><strong>Code:</strong> {{ $user->code }}</p>
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>Mobile No:</strong> {{ $user->mobile_no }}</p>
                                    <p><strong>Status:</strong> {{ ucfirst($user->status) }}</p>
                                </div>
                                <div class="col-md-6">
                                    @if($user->state)
                                        <p><strong>State:</strong> {{ $user->state->state ?? 'N/A' }}</p>
                                    @endif
                                    @if($user->city)
                                        <p><strong>City:</strong> {{ $user->city->name ?? 'N/A' }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                @can('AppUserManagement-Edit')
                                    <a href="{{ route('app-users.edit', $user->id) }}" class="btn btn-primary">Edit</a>
                                @endcan
                                <a href="{{ route('appUserManagement') }}" class="btn btn-secondary ms-2">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
@endsection
