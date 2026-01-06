@extends('admin::layouts.master')

@section('title', 'Non Active Members')

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
                    <h3><i class="las la-user-plus"></i>Non Active Members</h3>
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
                                            <th>Member Number</th>
                                            <th>City</th>
                                            <th>Region</th>
                                            <th>Deleted Date</th>
                                            <th>Deleted By</th>
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
                                                <td>{{ $user->member_number ?? '—' }}</td>
                                                <td>{{ $user->city ?? '—' }}</td>
                                                <td>{{ $user->region ?? '—' }}</td>
                                                <td>{{ $user->deleted_at ? \Carbon\Carbon::parse($user->deleted_at)->format('d M Y H:i') : '—' }}
                                                </td>
                                                <td>
                                                    @if ($user->deleted_by)
                                                        <span
                                                            class="deleted-by-badge {{ $user->deleted_by }}">{{ ucfirst($user->deleted_by) }}</span>
                                                    @else
                                                        <span>—</span>
                                                    @endif
                                                </td>

                                                <td class="text-center action-icons">
                                                    <a class="btn btn-warning btn-sm showBtn show-btn"
                                                        href="{{ route('admin.viewuser', [$user->id, 'nonActiveMembers']) }}"><i
                                                            class="ph ph-eye"></i></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-muted py-4">No non-active members
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
