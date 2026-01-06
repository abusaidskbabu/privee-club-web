@extends('admin::layouts.master')

@section('content')
    <style>
        /* ---- THEME COLORS ---- */
        :root {
            --theme-pink: #FFD0E7;
            --theme-dark: #4B0082;
        }

        .content-header h1 {
            color: #555;
            font-weight: 700;
        }

        .back-btn {
            background-color: var(--theme-pink);
            color: #333;
            border: none;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 25px;
            transition: 0.3s;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #f7bcd8;
            color: #000;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        .card-body label {
            font-weight: 600;
            color: #555;
        }

        .card-body div {
            font-size: 15px;
            color: #222;
        }

        .profile-img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .nav-tabs {
            border-bottom: 2px solid var(--theme-pink);
        }

        .nav-tabs .nav-link {
            color: #555;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--theme-pink);
            border-color: #555;
            border-radius: 5px 5px 0 0;
        }

        .user-image {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .fa-star,
        .fa-star-o {
            color: #FFD700;
            margin-right: 2px;
        }

        .table th {
            background-color: var(--theme-pink);
            color: #333;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        #ratingSubTabs .nav-link {
            border: none;
            color: #555;
            font-weight: 600;
        }

        #ratingSubTabs .nav-link.active {
            color: #4B0082;
            border-bottom: 3px solid #4B0082;
        }
    </style>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1>User Profile</h1>
                <a href="{{ url()->previous() }}" class="back-btn  text-black">← Back</a>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-3 text-center">
                                @php
                                    $profileImage = optional($user->profile)->profile_image;

                                    if ($profileImage && file_exists(public_path($profileImage))) {
                                        $imageUrl = asset($profileImage);
                                    } else {
                                        $imageUrl = asset('uploads/blankImage/blank.jpg');
                                    }
                                @endphp
                                {{-- <a href="{{ asset(optional($user->profile)->profile_image ?? 'uploads/blankImage/blank.jpg') }}"
                                    target="_blank">
                                    <img src="{{ asset(optional($user->profile)->profile_image ?? 'uploads/blankImage/blank.jpg') }}"
                                        class="profile-img" alt="Profile Image" style="cursor: pointer;">
                                </a> --}}

                                <a href="{{ $imageUrl }}" target="_blank">
                                    <img src="{{ $imageUrl }}" class="profile-img" alt="Profile Image"
                                        style="cursor:pointer;">
                                </a>
                                <h4>Profile Image</h4>
                            </div>

                            <div class="col-md-2">
                                <div class="d-flex flex-wrap justify-content-between">
                                    <div>
                                        <h3>{{ $user->first_name }} {{ $user->last_name }}</h3>
                                        <p class="text-muted mb-3">{{ ucfirst($user->gender) }} | {{ $user->city ?? 'N/A' }}
                                        </p>
                                        @if ($user->getRawOriginal('admin_status') == 1)
                                            <span class="badge badge-success">Approved</span>
                                        @elseif ($user->getRawOriginal('admin_status') == 2)
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-secondary">Pending Review</span>
                                        @endif
                                        @if ($user->getRawOriginal('admin_status') != 2 && $user->getRawOriginal('admin_status') != 1)
                                            <span class="badge badge-warning">Under User Approval</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @php
                                $profileImage =
                                    !empty($user->profile_image) && file_exists(public_path($user->profile_image))
                                        ? $user->profile_image
                                        : 'uploads/blankImage/blank.jpg';
                            @endphp
                            <div class="col-md-2 text-center">
                                <a href="{{ asset($profileImage) }}" target="_blank">
                                    <img src="{{ asset($profileImage) }}" class="profile-img" alt="Profile Image"
                                        style="cursor: pointer;">
                                </a>
                                <h4>Selfie</h4>
                            </div>

                            <div class="col-md-3 text-center">
                                @php
                                    $bestImage = optional($user->bestImage)->profile_image;

                                    if ($bestImage && file_exists(public_path($bestImage))) {
                                        $imageUrl = asset($bestImage);
                                    } else {
                                        $imageUrl = asset('uploads/blankImage/blank.jpg');
                                    }
                                @endphp

                                <a href="{{ $imageUrl }}" target="_blank">
                                    <img src="{{ $imageUrl }}" class="profile-img" alt="Profile Image"
                                        style="cursor:pointer;">
                                </a>
                                <h4>Best Image</h4>
                            </div>

                            <div class="col-md-2">
                                <div class="d-flex flex-wrap justify-content-between">
                                    <div class="">
                                        @if ($user->getRawOriginal('admin_status') != 2 && $user->getRawOriginal('admin_status') != 1)
                                            <span>Make Applicant</span>
                                            <select id="statusSelect" data-id="{{ $user->id }}" class="form-control"
                                                style="width:160px;border-radius:25px;">
                                                <option value=0 {{ $user->admin_status == 0 ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value=1 {{ $user->admin_status == 1 ? 'selected' : '' }}>Approve
                                                    User</option>
                                                <option value=2 {{ $user->admin_status == 2 ? 'selected' : '' }}>Reject
                                                </option>
                                            </select>
                                            <br>
                                        @endif
                                        @if ($user->getRawOriginal('profile_approval') == 0 && $user->getRawOriginal('profie_rating_status') == 'OUT')
                                            <span>Make Member</span>
                                            <select id="make_member" data-id="{{ $user->id }}" class="form-control"
                                                style="width:160px;border-radius:25px;">
                                                <option value=0 {{ $user->admin_status == 0 ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value=1 {{ $user->admin_status == 1 ? 'selected' : '' }}>Approve
                                                    Member</option>
                                                <option value=2 {{ $user->admin_status == 2 ? 'selected' : '' }}>Reject
                                                </option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MAIN TABS -->
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#details">Profile
                                    Details</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#public">Public Images</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#private">Private Images</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#ratings">Ratings</a></li>
                        </ul>

                        <div class="tab-content mt-4">

                            <!-- Profile Details -->
                            <div class="tab-pane fade show active" id="details">
                                <div class="row">
                                    @foreach ([
            'First Name' => $user->first_name,
            'Last Name' => $user->last_name,
            'Gender' => ucfirst($user->gender),
            'Date of Birth' => $user->dob,
            'Email' => $user->email,
            'Mobile Number' => $user->country_code . ' ' . $user->mobile_no,
            'Height' => $user->height,
            'Weight' => $user->weight,
            'Body Type' => ucfirst($user->body_type),
            'Hair Color' => $user->hair_color,
            'Eye Color' => $user->eye_color,
            'Nationality' => $user->nationality,
            'Region' => $user->region,
            'City' => $user->city,
            'Zodiac Sign' => $user->zodiac_sign,
            'Sexual Orientation' => $user->sexual_orientation,
        ] as $label => $value)
                                        <div class="col-md-4 mb-3">
                                            <label>{{ $label }}</label>
                                            <div>{{ $value ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Public Images -->
                            <div class="tab-pane fade" id="public">
                                <div class="row">
                                    @forelse($user->images->where('type',2) as $img)
                                        <div class="col-md-3 col-6 mb-3 text-center">
                                            <a href="{{ asset($img->profile_image) }}" target="_blank">
                                                <img src="{{ asset($img->profile_image) }}" class="user-image"
                                                    alt="" style="cursor: pointer;">
                                            </a>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted">No public images found.</div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Private Images -->
                            <div class="tab-pane fade" id="private">
                                <div class="row">
                                    @forelse($user->images->where('type',1) as $img)
                                        <div class="col-md-3 col-6 mb-3 text-center">
                                            <a href="{{ asset($img->profile_image) }}" target="_blank">
                                                <img src="{{ asset($img->profile_image) }}" class="user-image"
                                                    alt="" style="cursor: pointer;">
                                            </a>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted">No private images found.</div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Ratings Section -->
                            <div class="tab-pane fade" id="ratings" role="tabpanel">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <h5 class="mb-4">User Ratings</h5>

                                        <!-- Inner Rating Tabs -->
                                        <ul class="nav nav-tabs" id="ratingSubTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="ratings-given-tab"
                                                    data-bs-toggle="tab" data-bs-target="#ratings-given" type="button"
                                                    role="tab">⭐ Ratings Given</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ratings-received-tab" data-bs-toggle="tab"
                                                    data-bs-target="#ratings-received" type="button" role="tab">👤
                                                    Ratings Received</button>
                                            </li>
                                        </ul>

                                        <div class="tab-content mt-4" id="ratingSubTabsContent">
                                            <!-- Ratings Given -->
                                            <div class="tab-pane fade show active" id="ratings-given" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>S.NO</th>
                                                                <th>Id</th>

                                                                <th>Given To</th>

                                                                <th>Username</th>

                                                                <th>Reaction</th>

                                                                <th>Points</th>

                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($rategiven as $index => $rating)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $rating->ratedTo->id ?? '-' }}</td>

                                                                    <td>{{ $rating->ratedTo->first_name ?? '-' }}</td>
                                                                    <td>{{ $rating->ratedTo->profile_name ?? '-' }}</td>
                                                                    <td>{{ $rating->reaction ?? '-' }}</td>
                                                                    <td>{{ $rating->points ?? '-' }}</td>
                                                                    <td>{{ optional($rating->created_at)->format('d M Y') }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center text-muted">No
                                                                        ratings given.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Ratings Received -->
                                            <div class="tab-pane fade" id="ratings-received" role="tabpanel">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>S.NO</th>
                                                                <th>Id</th>

                                                                <th>Received by</th>
                                                                <th>Username</th>

                                                                <th>Reaction</th>

                                                                <th>Points</th>

                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($raterecived as $index =>$rating)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $rating->ratedBy->id }}</td>

                                                                    <td>{{ $rating->ratedBy->first_name ?? 'N/A' }}</td>

                                                                    <td>{{ $rating->ratedBy->profile_name ?? 'N/A' }}</td>
                                                                    <td>{{ $rating->reaction == 'YES' ? 'OH YES' : $rating->reaction }}
                                                                    </td>
                                                                    <td>{{ $rating->points ?? '-' }}</td>
                                                                    <td>{{ optional($rating->created_at)->format('d M Y') }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center text-muted">No
                                                                        ratings received.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div> <!-- inner tab content -->
                                    </div>
                                </div>
                            </div>
                        </div> <!-- tab content -->
                    </div>
                </div>
            </div>
        </section>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#statusSelect').on('change', function() {
                let user_id = $(this).data('id');
                let status = $(this).val();
                let badge = $(this).closest('.d-flex.flex-wrap').find('.badge');

                $.ajax({
                    url: "{{ url('update-adminstatus') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: user_id,
                        admin_status: status
                    },
                    success: function(response) {
                        if (response.status) {
                            badge.removeClass('badge-success badge-danger badge-secondary');
                            if (status == 1) badge.addClass('badge-success').text('Approved');
                            else if (status == 2) badge.addClass('badge-danger').text(
                                'Rejected');
                            else badge.addClass('badge-secondary').text('Pending Review');
                        } else {
                            alert('⚠️ ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong.');
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#make_member').on('change', function() {
                let user_id = $(this).data('id');
                let status = $(this).val();
                let badge = $(this).closest('.d-flex.flex-wrap').find('.badge');

                $.ajax({
                    url: "{{ route('admin.make.member') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: user_id,
                        admin_status: status
                    },
                    success: function(response) {
                        if (response.status) {
                            badge.removeClass('badge-success badge-danger badge-secondary');
                            if (status == 1) badge.addClass('badge-success').text('Approved');
                            else if (status == 2) badge.addClass('badge-danger').text(
                                'Rejected');
                            else badge.addClass('badge-secondary').text('Pending Review');
                        } else {
                            alert('⚠️ ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong.');
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
