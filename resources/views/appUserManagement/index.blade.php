@extends('layouts.main')
@section('title', 'App User Management - Singhal Steel')
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
                        <span class="text text-2">User Management Update Unsuccessful</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        <div class="dashboard-header pagetitle">
            <h1>App User Management</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">App User Management</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">User List</h5>
                            <div class="table-responsive">
                                <table id="app-user-table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text__left">#</th>
                                            <th class="text__left">Name</th>
                                            <th class="text__left">Type</th>
                                            <th class="text__left">Code</th>
                                            <th class="text__left">Email</th>
                                            <th class="text__left">Mobile No</th>
                                            <th class="text__left">Status</th>
                                            <th class="text__left">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $i=>$user)
                                            <tr>
                                                <td class="text__left">{{ $i+1 }}</td>
                                                <td class="text__left">{{ $user->name }}</td>
                                                <td class="text__left">{{ $user->type }}</td>
                                                <td class="text__left">{{ $user->code }}</td>
                                                <td class="text__left">{{ $user->email }}</td>
                                                <td class="text__left">{{ $user->mobile_no }}</td>
                                                <td class="text__left">{{ ucfirst($user->status) }}</td>
                                                <td class="text__left">
                                                    @can('AppUserMgmt-View')
                                                        <a href="{{ route('app-users.show', $user->id) }}" class="btn btn-info btn-sm">View</a>
                                                    @endcan
                                                    @can('AppUserMgmt-Edit')
                                                        <a href="{{ route('app-users.edit', $user->id) }}" class="btn btn-primary btn-sm ms-2">Edit</a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <h2>No Users Found</h2>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination (if applicable) -->
                            {{-- {{ $users->links() }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
@endsection
