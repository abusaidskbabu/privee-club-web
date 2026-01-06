@extends('admin::layouts.master')

@section('title', isset($member) && $member->id ? 'Update Ready Member' : 'Add Ready Member')

@section('css')
    <style>
        .page-header {
            background: linear-gradient(90deg, #f5f2ff, #ebe5ff);
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 12px rgba(95, 66, 170, 0.15);
        }

        .page-header h3 {
            color: #5f42aa;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            margin: 0;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .alert ul {
            padding: 0 !important;
            list-style-type: none !important;
            color: #fff;
        }

        .alert .close {
            color: #fff !important;
            opacity: .2;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="container-fluid  d-flex justify-content-between align-items-center">
                        <h3><i class="fas fa-user-plus"></i>
                            {{ isset($member) && $member->id ? 'Update Ready Member' : 'Add Ready Member' }}</h3>
                        <button class="btn btn-primary text-black">
                            <a href="{{ route('admin.ready.members') }}" class="back-btn"
                                style="color: black; text-decoration: none;">← Back</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="tab-content" id="genderTabsContent">
                    <div class="tab-pane fade show active" id="male" role="tabpanel" aria-labelledby="male-tab">
                        @if ($errors->any())
                            <div class="card  mt-3">
                                <div class="card-body">
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <div class="card mt-3">
                            <div class="card-body table-responsive">
                                <form
                                    action="{{ isset($member) && $member->id ? route('admin.edit.ready.member', base64_encode($member->id)) : route('admin.add.ready.member') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <!-- Basic Information -->
                                        <div class="col-md-6">
                                            <h5 class="mb-3"
                                                style="color: #5f42aa; border-bottom: 2px solid #e5e0ff; padding-bottom: 10px;">
                                                Basic Information</h5>

                                            <div class="form-group mb-3">
                                                <label for="profile_image">Profile Image</label>
                                                @if (isset($member) && $member->id)
                                                    @php
                                                        $profileImg = optional($member->profile)->profile_image
                                                            ? asset(optional($member->profile)->profile_image)
                                                            : asset('assets/images/default-150x150.png');
                                                    @endphp
                                                    <div class="mb-2">
                                                        <img src="{{ $profileImg }}" id="profileImagePreview"
                                                            style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e0ff; cursor: pointer;"
                                                            onclick="document.getElementById('profile_image').click()"
                                                            title="Click to change image">
                                                    </div>
                                                @else
                                                    <div class="mb-2">
                                                        <img src="{{ asset('assets/images/default-150x150.png') }}"
                                                            id="profileImagePreview"
                                                            style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e0ff; cursor: pointer;"
                                                            onclick="document.getElementById('profile_image').click()"
                                                            title="Click to select image">
                                                    </div>
                                                @endif
                                                <input type="file" name="profile_image" id="profile_image"
                                                    class="form-control" accept="image/*" style="display: none;"
                                                    onchange="previewProfileImage(this)">
                                                <small class="form-text text-muted">Click on image to change. JPG, PNG,
                                                    GIF
                                                    (max 2MB)</small>
                                                @error('profile_image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="first_name">First Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="first_name" class="form-control"
                                                    value="{{ old('first_name', isset($member) && $member->id ? $member->first_name : '') }}"
                                                    placeholder="Enter first name" required>
                                                @error('first_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" name="last_name" class="form-control"
                                                    value="{{ old('last_name', isset($member) && $member->id ? $member->last_name : '') }}"
                                                    placeholder="Enter last name" required>
                                                @error('last_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="gender">Gender <span class="text-danger">*</span></label>
                                                <select name="gender" class="form-control" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male"
                                                        {{ old('gender', isset($member) && $member->id ? $member->gender : '') == 'Male' ? 'selected' : '' }}>
                                                        Male</option>
                                                    <option value="Female"
                                                        {{ old('gender', isset($member) && $member->id ? $member->gender : '') == 'Female' ? 'selected' : '' }}>
                                                        Female</option>
                                                </select>
                                                @error('gender')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="dob">Date of Birth <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="dob" class="form-control"
                                                    value="{{ old('dob', isset($member) && $member->id ? $member->dob : '') }}"
                                                    required>
                                                @error('dob')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Physical Attributes -->
                                        <div class="col-md-6">
                                            <h5 class="mb-3"
                                                style="color: #5f42aa; border-bottom: 2px solid #e5e0ff; padding-bottom: 10px;">
                                                Physical Attributes</h5>

                                            <div class="form-group mb-3">
                                                <label for="height">Height</label>
                                                <input type="text" name="height" class="form-control"
                                                    value="{{ old('height', isset($member) && $member->id ? $member->height : '') }}"
                                                    placeholder="Enter height">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="weight">Weight</label>
                                                <input type="text" name="weight" class="form-control"
                                                    value="{{ old('weight', isset($member) && $member->id ? $member->weight : '') }}"
                                                    placeholder="Enter weight">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="body_type">Body Type</label>
                                                <select name="body_type" class="form-control">
                                                    <option value="">Select Body Type</option>
                                                    @foreach ($bodyTypes as $bodyType)
                                                        <option value="{{ $bodyType->body_type }}"
                                                            {{ old('body_type', isset($member) && $member->id ? $member->body_type : '') == $bodyType->body_type ? 'selected' : '' }}>
                                                            {{ $bodyType->body_type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="hair_color">Hair Color</label>
                                                <input type="text" name="hair_color" class="form-control"
                                                    value="{{ old('hair_color', isset($member) && $member->id ? $member->hair_color : '') }}"
                                                    placeholder="Enter hair color">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="eye_color">Eye Color</label>
                                                <input type="text" name="eye_color" class="form-control"
                                                    value="{{ old('eye_color', isset($member) && $member->id ? $member->eye_color : '') }}"
                                                    placeholder="Enter eye color">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <!-- Location & Preferences -->
                                        <div class="col-md-6">
                                            <h5 class="mb-3"
                                                style="color: #5f42aa; border-bottom: 2px solid #e5e0ff; padding-bottom: 10px;">
                                                Location & Preferences</h5>

                                            <div class="form-group mb-3">
                                                <label for="nationality">Nationality</label>
                                                <select name="nationality" class="form-control">
                                                    <option value="">Select Nationality</option>
                                                    @foreach ($nationalities as $nationality)
                                                        <option value="{{ $nationality->nationality }}"
                                                            {{ old('nationality', isset($member) && $member->id ? $member->nationality : '') == $nationality->nationality ? 'selected' : '' }}>
                                                            {{ $nationality->nationality }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="region">Region</label>
                                                <select name="region" id="region" class="form-control">
                                                    <option value="">Select Region</option>
                                                    @foreach ($regions as $region)
                                                        <option value="{{ $region->region }}"
                                                            data-country="{{ optional($region->country)->country ?? '' }}"
                                                            {{ old('region', isset($member) && $member->id ? $member->region : '') == $region->region ? 'selected' : '' }}>
                                                            {{ $region->region }} @if ($region->country)
                                                                ({{ $region->country->country }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="city">City</label>
                                                <select name="city" id="city" class="form-control">
                                                    <option value="">Select City</option>
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->city }}"
                                                            data-region="{{ $city->region->region ?? '' }}"
                                                            {{ old('city', isset($member) && $member->id ? $member->city : '') == $city->city ? 'selected' : '' }}>
                                                            {{ $city->city }} @if ($city->region)
                                                                ({{ $city->region->region }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="zodiac_sign">Zodiac Sign</label>
                                                <select name="zodiac_sign" class="form-control">
                                                    <option value="">Select Zodiac Sign</option>
                                                    @foreach ($zodiacs as $zodiac)
                                                        <option value="{{ $zodiac->Zodiac_Signs }}"
                                                            {{ old('zodiac_sign', isset($member) && $member->id ? $member->zodiac_sign : '') == $zodiac->Zodiac_Signs ? 'selected' : '' }}>
                                                            {{ $zodiac->Zodiac_Signs }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="sexual_orientation">Sexual Orientation</label>
                                                <select name="sexual_orientation" class="form-control">
                                                    <option value="">Select Sexual Orientation</option>
                                                    @foreach ($sexOrientations as $orientation)
                                                        <option value="{{ $orientation->sex_orientation }}"
                                                            {{ old('sexual_orientation', isset($member) && $member->id ? $member->sexual_orientation : '') == $orientation->sex_orientation ? 'selected' : '' }}>
                                                            {{ $orientation->sex_orientation }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- About & Status -->
                                        <div class="col-md-6">
                                            <h5 class="mb-3"
                                                style="color: #5f42aa; border-bottom: 2px solid #e5e0ff; padding-bottom: 10px;">
                                                About & Status</h5>

                                            <div class="form-group mb-3">
                                                <label for="about_me">About Me</label>
                                                <textarea name="about_me" class="form-control" rows="4" placeholder="Enter about me">{{ old('about_me', isset($member) && $member->id ? $member->about_me : '') }}</textarea>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="about_match">My Perfect Match</label>
                                                <textarea name="about_match" class="form-control" rows="4" placeholder="Enter perfect match description">{{ old('about_match', isset($member) && $member->id ? $member->about_match : '') }}</textarea>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>Status</label>
                                                <div>
                                                    <label class="switch">
                                                        <input type="checkbox" name="is_active"
                                                            {{ old('is_active', isset($member) && $member->id && $member->is_active == 1) ? 'checked' : '' }}>
                                                        <span class="slider"></span>
                                                    </label>
                                                    <span class="ml-2">Active</span>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>Show on Mobile</label>
                                                <div>
                                                    <label class="switch">
                                                        <input type="checkbox" name="show_on_mobile"
                                                            {{ old('show_on_mobile', isset($member) && $member->id && $member->show_on_mobile == 1) ? 'checked' : '' }}>
                                                        <span class="slider"></span>
                                                    </label>
                                                    <span class="ml-2">Yes</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i>
                                            {{ isset($member) && $member->id ? 'Update Ready Member' : 'Add Ready Member' }}
                                        </button>
                                        <a href="{{ route('admin.ready.members') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function previewProfileImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#profileImagePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Filter cities based on selected region
            $('#region').on('change', function() {
                var selectedRegion = $(this).val();
                $('#city option').each(function() {
                    var cityRegion = $(this).data('region');
                    if (selectedRegion && cityRegion !== selectedRegion) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
                $('#city').val(''); // Reset city selection
            });

            // Trigger on page load if region is already selected
            if ($('#region').val()) {
                $('#region').trigger('change');
            }
        });
    </script>
@endsection
