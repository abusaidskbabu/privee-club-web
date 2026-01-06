@extends('admin::layouts.master')

@section('title', 'Rejected Users')

@section('css')
    <style>
        .timer {
            font-weight: bold;
            font-size: 16px;
        }

        .timer.expired {
            color: #b70000;
        }

        .timer.green {
            color: green;
        }

        .timer.orange {
            color: orange;
        }

        .timer.red {
            color: red;
        }

        .timer.expired {
            color: #b70000;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <h3><i class="las la-user-plus"></i>Rejected Users</h3>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">


                <div class="tab-content" id="genderTabsContent">

                    <!-- Male Users -->
                    <div class="tab-pane fade show active" id="male" role="tabpanel" aria-labelledby="male-tab">
                        <div class="card mt-3">
                            <div class="card-body table-responsive">

                                <div class="form-group mb-3">
                                    <label for="genderFilter" class="font-weight-bold">Filter by Gender:</label>
                                    <select id="genderFilter" class="form-select" style="width: 200px;">
                                        <option value="">All Users</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>

                                <table class="table table-striped align-middle commontable">


                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Member Number</th>
                                            <th>Profile</th>
                                            <th>User Name</th>
                                            <th>Gender</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Registered On</th>


                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($Users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $user->member_number ?? '—' }}</td>
                                                <td>
                                                    @php
                                                        $imageUrl = asset(
                                                            ($img = preg_replace(
                                                                '#^public/#',
                                                                '',
                                                                optional($user->profile)->profile_image ?? '',
                                                            )) && file_exists(public_path($img))
                                                                ? $img
                                                                : 'uploads/blankImage/blank.jpg',
                                                        );
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" alt="Profile Image"
                                                        style="height:80px; width:80px; border-radius:50%; object-fit:cover; border:2.5px solid #e1dbff; margin:0 auto;">
                                                </td>
                                                <td>{{ $user->profile_name ?? '—' }}</td>
                                                <td>{{ $user->gender ?? '—' }}</td>
                                                <td>{{ $user->email ?? '—' }}</td>
                                                <td>{{ $user->mobile_no ?? '—' }}</td>
                                                <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</td>

                                                <td class="text-center action-icons" style="min-width: 70px;">
                                                    <a class="btn btn-warning btn-sm showBtn show-btn"
                                                        href="{{ route('admin.viewuser', [$user->id, 'rejectedUsers']) }}"><i
                                                            class="ph ph-eye"></i></a>
                                                    <button href="javascript:void(0)"
                                                        class="deleteBtn btn btn-sm btn-danger delete-btn "
                                                        data-id="{{ $user->id }}"><i class="ph ph-trash"></i></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">No rejected users
                                                    found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>


    <script>
        $(document).on('click', '.delete-btn', function() {
            let userId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete the user!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('destroy', ['id' => ':id']) }}".replace(':id', userId),
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $(`.delete-btn[data-id="${userId}"]`).closest('tr').fadeOut();
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
    <script>
        $(document).ready(function() {
            let table = $('.commontable').DataTable();

            $('#genderFilter').on('change', function() {
                let selectedGender = $(this).val();
                if (selectedGender === "") {
                    table.column(4).search("").draw(); // Reset filter
                } else {
                    table.column(4).search('^' + selectedGender + '$', true, false).draw(); // Exact match
                }
            });
        });
    </script>

@endsection
