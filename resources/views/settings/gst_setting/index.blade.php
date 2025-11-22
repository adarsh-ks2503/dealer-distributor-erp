@extends('layouts.main')
@section('title', 'Index GST Settings - SunilSteel')
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
        @if ($message = Session::get('error'))
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-exclamation exclamation update"></i>
                    <div class="message">
                        <span class="text text-1">Error</span>
                        <span class="text text-2"> {{ $message }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif



        <div class="dashboard-header pagetitle">
            <h1>GST Settings</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">GST Settings</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">


            <div class="row">



                <div class="col-lg-12 mt-3">

                    <div class="card">
                        <div class="card-body mt-5">
                            <!-- All Items Tab -->
                            <div class="row ">
                                <div class="col-md-6 col-sm-12">
                                    <div class="pd-20">
                                        <h4 class="text-blue h4">GST Settings</h4>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 d-flex justify-content-end ">
                                    <div class="btn-group">
                                        @can('GST-Create')
                                            <a class="btn btn-primary mb-4 mr-3" data-bs-toggle="modal"
                                                data-bs-target="#modal1">
                                                Add</a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            <!-- Table with stripped rows -->
                            <table class="display stripe row-border order-column" id="gst_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text__left">S. No</th>
                                        <th class="text__left">Prefix</th>
                                        <th class="text__left">%</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gsts as $gst)
                                        <tr>
                                            <td class="text__left">{{ $loop->iteration }}</td>
                                            <td class="text__left">{{ $gst->gst_prefix }}</td>
                                            <td class="text__left">{{ $gst->percent }}</td>
                                            @if ($gst->deleted_at == null)
                                                <td>
                                                    Active
                                                </td>
                                            @else
                                                <td>
                                                    Inactive
                                                </td>
                                            @endif
                                            <td>
                                                <!-- 3 dots action menu -->
                                                <div class="dropdown">
                                                    <button class="btn btn-light border-0" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">


                                                        @if ($gst->deleted_at == null)
                                                            @can('GST-Edit')
                                                                <li>
                                                                    <a class="dropdown-item" href=""
                                                                        data-bs-toggle="modal"
                                                                        onclick="get_data({{ $gst->id }})"
                                                                        data-bs-target="#modal2">
                                                                        <i class="fa-solid fa-pencil"></i>Edit</a>
                                                                </li>
                                                            @endcan
                                                            @can('GST-InActive')
                                                                <li>

                                                                    <form action="{{ route('gst-setting.destroy', $gst->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button"
                                                                            class="dropdown-item inactive-button">
                                                                            <i class="fas fa-ban me2"></i> InActive
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endcan
                                                        @else
                                                            @can('GST-Active')
                                                                <li>
                                                                    <form action="{{ route('gstsetting.activate', $gst->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button"
                                                                            class="dropdown-item activate-button">
                                                                            <i class="fa-solid fa-rotate-right me2"></i> Active
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endcan
                                                        @endif

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
            </div>
        </section>


        <!-- Modal 1 -->
        <div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal1Label">ADD GST</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width:50px"></button>
                    </div>
                    <form action="{{ route('gst-setting.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row  ">
                                <label for="inputPassword3" class="col-sm-12 col-form-label"><strong>GST Prefix</strong>
                                    <span class="required-classes">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" name="gst_prefix" id="set_po_id"
                                        required>
                                </div>

                                <label for="inputPassword3" class="col-sm-12 col-form-label"><strong>GST %</strong><span
                                        class="required-classes">*</span> </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" name="gst_percent" id="set_po_id"
                                        required max="100">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal 1 -->
        <div class="modal fade" id="modal2" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal1Label">Edit GST</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="width:50px"></button>
                    </div>
                    <form action="{{ route('gstsetting.update') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row  ">
                                <label for="inputPassword3" class="col-sm-12 col-form-label"><strong>GST Prefix</strong>
                                    <span class="required-classes">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" name="gst_prefix" id="gst_prefix"
                                        required>
                                    <input type="hidden" class="form-control" name="gst_id" id="gst_id" required>
                                </div>

                                <label for="inputPassword3" class="col-sm-12 col-form-label"><strong>GST %</strong><span
                                        class="required-classes">*</span> </label>
                                <div class="col-sm-12">
                                    <input type="number" class="form-control" name="gst_percent" id="gst_percent"
                                        max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">

                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </main><!-- End #main -->

    <script>
        function get_data(gst_id) {

            let item_id = gst_id;

            $.ajax({
                url: "{{ url('get_gst_details') }}",
                method: "POST",
                data: {
                    item_id: item_id,
                    "_token": "{{ csrf_token() }}",
                },

                success: function(res) {
                    if (res) {
                        let gst_prefix = $('#gst_prefix');
                        let gst_percent = $('#gst_percent');
                        let gst_id = $('#gst_id');


                        gst_prefix.val(res.data.gst_prefix);
                        gst_percent.val(res.data.percent);
                        gst_id.val(res.data.id);
                    }
                }


            });
        }
    </script>
@endsection
