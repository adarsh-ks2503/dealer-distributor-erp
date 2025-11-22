@extends('layouts.main')
@section('title', ' Role Management- Singhal Steel')
@section('content')
    <main id="main" class="main">



        @if ($message = Session::get('success'))
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

        @if ($message = Session::get('update'))
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-check check"></i>
                    <div class="message">
                        <span class="text text-1">Update</span>
                        <span class="text text-2"> {{ $message }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        @if ($message = Session::get('delete'))
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-exclamation exclamation update"></i>
                    <div class="message">
                        <span class="text text-1">Delete</span>
                        <span class="text text-2"> {{ $message }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif

        <div class="dashboard-header pagetitle">
            <h1>Role Management</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Role Management</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">


            <div class="row">

                <div class="col-lg-12">

                    <div class="card pt-4">
                        <div class="card-body">
                            <div class="row ">
                                <div class="col-md-6 col-sm-12">
                                    <div class="pd-20">
                                        <h4 class="text-blue h4">Role Management</h4>

                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex justify-content-end ">

                                    <div class="btn-group">
                                        @can('AccessManagement-Create')
                                            <a class="btn btn-primary mb-4 mr-3 "href="{{ route('roles.create') }}">Add New
                                                Role</a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            <!-- Table with stripped rows -->
                            <table id="Role_Management_table" class="display stripe row-border order-column">
                                <thead>
                                    <tr>

                                        <th class="text__left">S. No</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr id="role_table_td_{{ $role->id }}">
                                            <td class="text__left">{{ $loop->iteration }}
                                            </td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                <div class="filter">
                                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                            class="bi bi-three-dots"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                        @can('AccessManagement-View')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('roles.show', $role->id) }}"><i
                                                                        class="fa-regular fa-eye"></i> View</a>
                                                            </li>
                                                        @endcan
                                                        <li>
                                                            @can('AccessManagement-Edit')
                                                                @if ($role->id != 1)
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('roles.edit', $role->id) }}"><i
                                                                            class="fa-solid fa-pencil"></i>Edit</a>
                                                                @endif
                                                            @endcan
                                                        </li>
                                                        <li>
                                                            @can('AccessManagement-Delete')
                                                                @if ($role->id != 1)
                                                                    <form method="POST"
                                                                        action="{{ route('roles.destroy', $role->id) }}">
                                                                        @csrf
                                                                        @method('GET')
                                                                        <button type="button"
                                                                            class="dropdown-item delete-button">
                                                                            <i class="fa-solid fa-trash"></i> Delete
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endcan
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->


@endsection
