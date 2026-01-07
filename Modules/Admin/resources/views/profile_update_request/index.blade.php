@extends('admin::layouts.master')
@section('title', 'ðŸ†• New Users')
@section('css')
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <h3><i class="las la-user-plus"></i> Photo Update Request</h3>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="tab-content" id="genderTabsContent">
                    <!-- ðŸ‘¨ Male Users -->
                    <div class="tab-pane fade show active" id="male" role="tabpanel" aria-labelledby="male-tab">
                        <div class="card mt-3">
                            <div class="card-body table-responsive">
                                <table class="table table-striped align-middle commontable">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>User Name</th>
                                            <th>New Photo</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($allRequest as $key => $val)
                                            <tr id="row-{{ $val->id }}">
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $val->user->first_name ?? '' }} {{ $val->user->last_name ?? '' }}
                                                </td>

                                                @php
                                                    $imagePath =
                                                        !empty($val->profile_image) &&
                                                        file_exists(public_path($val->profile_image))
                                                            ? $val->profile_image
                                                            : 'uploads/blankImage/blank.jpg';
                                                @endphp
                                                <td>
                                                    <img src="{{ url($imagePath) }}" width="50" height="50">
                                                </td>
                                                <td>{{ optional($val->created_at)->format('d-m-Y H:i:s') }}</td>
                                                <td>
                                                    <button class="btn btn-success approve-btn btn-sm action-btn"
                                                        data-id="{{ $val->id }}" data-status="1">
                                                        <i class="ph ph-check"></i>
                                                    </button>
                                                    <a href="{{ route('admin.viewuser', [$val->user_id, 'profileUpdateRequest']) }}"
                                                        class="btn btn-warning btn-sm showBtn show-btn"><i
                                                            class="ph ph-eye"></i></a>

                                                    <button class="ms-2 btn btn-sm btn-danger delete-btn action-btn"
                                                        data-id="{{ $val->id }}" data-status="2">
                                                        <i class="ph ph-trash"></i>
                                                    </button>


                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@section('script')

    <script>
        $(document).on('click', '.action-btn', function() {

            let id = $(this).data('id');
            let status = $(this).data('status');
            let actionText = status == 1 ? "approve" : "reject";

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + actionText + ' this photo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: status == 1 ? '#28a745' : '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, ' + actionText + ' it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('admin.image.approve.reject') }}",
                        type: "POST",
                        data: {
                            id: id,
                            status: status,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire(
                                    actionText.charAt(0).toUpperCase() + actionText.slice(
                                        1) + 'd!',
                                    response.message,
                                    'success'
                                );

                                $("#row-" + id).fadeOut(300, function() {
                                    $(this).remove();
                                    location.reload();
                                });

                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });

                }
            });

        });
    </script>
@endsection
@endsection
