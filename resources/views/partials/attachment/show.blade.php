@if ($selected_images->isNotEmpty())

    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 mt-4 overflow-auto" style="max-height: 600px;">

                    <h3 class="mb-4">Attachment Details</h3>


                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:50%">File</th>
                                <th style="width:50%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selected_images as $img)
                                <tr>
                                    <td>
                                        @if (!empty($img->file_path))
                                            @php
                                                $filename = $img->file_name ?? basename($img->file_path);
                                                $filePath = 'storage/' . $img->file_path;
                                            @endphp

                                            <a href="{{ asset($filePath) }}" class="link-primary text-primary"
                                                target="_blank">
                                                {{ $filename }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $img->atch_remarks ?? 'N/A' }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        @if ($cancelRoute != 'dispatch_show')
            <div class="text-end p-4">
                <a href="{{ $cancelRoute ?? '#' }}" class="btn btn-secondary mt-3">Back</a>
            </div>
        @endif

    </div>
@endif
