@extends('layouts.main')
@section('title', ' Users View- Singhal Steel')
@section('content')
    <main id="main" class="main">
        <div class="dashboard-header pagetitle">
            <h1>User Details</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User Management</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card pt-4">
                        <div class="card-body">


                            <div class="row">
                                <div class="col-lg-6 mt-4">
                                    <h3 class="mb-4">User Details</h3>

                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>First Name</th>
                                                <td>{{ $user->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Name</th>
                                                <td>{{ $user->last_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Mobile</th>
                                                <td>{{ $user->mobile ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $user->email ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Role</th>
                                                <td>
                                                    @if (!empty($user->getRoleNames()))
                                                        @foreach ($user->getRoleNames() as $v)
                                                            <label class="badge bg-success">{{ $v }}</label>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>


                                        </tbody>
                                    </table>


                                </div>

                            </div>
                            <div class="text-right">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
