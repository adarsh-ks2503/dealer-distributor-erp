@extends('layouts.main')
@section('title','Role View- Singhal Steel')
@section('content')
    <main id="main" class="main">
        <div class="dashboard-header pagetitle">
            <h1>Role Management Details</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role Management</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card pt-4" >
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h4 class="card-title">Show Role</h4>
                                </div>
                                <div class="col-lg-6 d-flex justify-content-end ">
                                    <a class="btn btn-primary mb-4 mr-3 "href="{{ route('roles.index') }}">Back</a>
                                </div>
                            </div>
                            <!-- Multi Columns Form -->
                            <div class="col-md-4">
                                <label for="name"><strong>Name : </strong> {{ $role->name }}</label>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="permission"><strong>Permission:</strong></label>
                                    @foreach ($rolePermissions as $value)
                                        <label>
                                            {{ $value->name }} ,
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
