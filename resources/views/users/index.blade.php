@extends('layouts.main')
@section('title', ' User Management- Singhal Steel')
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
            <h1>User Management Details</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">User Management</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">


            <div class="row">

                <div class="col-lg-12">

                    <div class="card pt-4">
                        <div class="card-body">
                            <div class="row pt-4">
                                <div class="col-md-6 col-sm-12">
                                    <div class="pd-20">
                                        <h4 class="text-blue h4">User Management</h4>
                                        <em style="color: red"> Note: Super Admin can't be deleted</em><br>

                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex justify-content-end ">
                                    <div class="me-3 add_btn_row_in_user_table" style="display: none">
                                        <div class="col-lg-12">
                                            @can('UserManagement-View')
                                                <a class="btn btn-primary" id="user_table_view_btn">
                                                    View</a>
                                            @endcan
                                            @can('UserManagement-Edit')
                                                <a class="btn btn-success" id="user_table_edit_btn">
                                                    Edit</a>
                                            @endcan
                                            @can('UserManagement-InActive')
                                                <a class="btn btn-danger " id="user_table_delete_btn">
                                                    Delete</a>
                                            @endcan
                                        </div>
                                    </div>
                                    @can('UserManagement-Create')
                                        <div class="btn-group">
                                            <a class="btn btn-primary mb-4 mr-3 "href="{{ route('users.create') }}">Add New
                                                User</a>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                            <!-- Table with stripped rows -->
                            <table id="User_Management_table" class="display stripe row-border order-column"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text__left">S. No</th>
                                        {{-- <th>Employee ID</th> --}}
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th class="text__left">Mobile No.</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $user)
                                        <tr>
                                            <td class="text__left">{{ $loop->iteration }}</td>
                                            {{-- <td> {{ $user->employee_id ?? 'N/A' }}</td> --}}
                                            <td>{{ $user->name }} {{ $user->last_name }}</td>
                                            <td>
                                                @if (!empty($user->getRoleNames()))
                                                    @foreach ($user->getRoleNames() as $v)
                                                        <label class="badge bg-success">{{ $v }}</label>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text__left">{{ $user->mobile }}</td>
                                            <td>{{ $user->email }}</td>
                                            @if ($user->deleted_at != null)
                                                <td><span class="badge bg-danger">Inactive</span></td>
                                            @else
                                                <td><span class="badge bg-success">Active</span></td>
                                            @endif
                                            <td>
                                                <div class="filter">
                                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                            class="bi bi-three-dots"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                        @can('UserManagement-View')
                                                            <li> <a class="dropdown-item"
                                                                    href="{{ route('users.show', $user->id) }}"><i
                                                                        class="fa-regular fa-eye"></i> View</a>
                                                            </li>
                                                        @endcan

                                                        @can('UserManagement-Edit')
                                                            <li>
                                                                @if ($user->id != 1)
                                                                    @if ($user->deleted_at == null)
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('users.edit', $user->id) }}"><i
                                                                                class="fa-solid fa-pencil"></i>Edit</a>
                                                                    @endif
                                                                @endif
                                                            </li>
                                                        @endcan
                                                        <li>
                                                            @if ($user->id != 1)
                                                                @if ($user->deleted_at != null)
                                                                    {{-- <a class="dropdown-item"
                                                            href="{{ route('users.resoter', $user->id) }}"><i
                                                            class="fa-solid fa-rotate-right"></i>Activate</a> --}}
                                                                    @can('UserManagement-Active')
                                                                        <form method="POST"
                                                                            action="{{ route('users.resoter', $user->id) }}">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button"
                                                                                class="dropdown-item activate-button">
                                                                                <i class="fa-solid fa-rotate-right"></i>
                                                                                Activate
                                                                            </button>
                                                                        </form>
                                                                    @endcan
                                                                @else
                                                                    @can('UserManagement-InActive')
                                                                        <form method="POST"
                                                                            action="{{ route('users.destroy', $user->id) }}">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button"
                                                                                class="dropdown-item inactive-button">
                                                                                <i class="fa-solid fa-power-off"></i> Inactive
                                                                            </button>
                                                                        </form>
                                                                    @endif
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
