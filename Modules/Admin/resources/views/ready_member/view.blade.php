@extends('admin::layouts.master')

@section('content')
<style>
/* ---- THEME COLORS ---- */
:root {
    --theme-pink: #FFD0E7;
    --theme-dark: #4B0082;
}
.content-header h1 { color: #555; font-weight: 700; }
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
.back-btn:hover { background-color: #f7bcd8; color: #000; }
.card { border: none; border-radius: 15px; box-shadow: 0 5px 10px rgba(0,0,0,0.15); }
.card-body label { font-weight: 600; color: #555; }
.card-body div { font-size: 15px; color: #222; }
.profile-img {
    width: 160px; height: 160px; border-radius: 50%;
    object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
.nav-tabs { border-bottom: 2px solid var(--theme-pink); }
.nav-tabs .nav-link {
    color: #555; font-weight: 600; border: none;
    border-bottom: 3px solid transparent; border-radius: 0;
}
.nav-tabs .nav-link.active {
    background-color: var(--theme-pink);
    border-color: #555; border-radius: 5px 5px 0 0;
}
.user-image {
    border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    width: 100%; height: 200px; object-fit: cover;
}
.table th { background-color: var(--theme-pink); color: #333; font-weight: 600; }
.table td { vertical-align: middle; }
.image-container {
    position: relative;
    display: inline-block;
    margin: 10px;
}
.image-container img {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    width: 200px;
    height: 200px;
    object-fit: cover;
}
.image-container .delete-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.image-container .delete-image:hover {
    background: rgba(220, 53, 69, 1);
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Ready Member Profile</h1>
            <a href="{{ route('admin.ready.members') }}" class="back-btn  text-black">← Back</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center">
                           <img src="{{ asset(optional($member->profile)->profile_image ?? 'assets/images/default-150x150.png') }}" class="profile-img" alt="Profile Image">
                           <h4>Profile Image</h4>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-wrap justify-content-between">
                                <div>
                                    <h3>{{ $member->first_name }} {{ $member->last_name }}</h3>
                                    <p class="text-muted mb-3">{{ ucfirst($member->gender) }} | {{ $member->city ?? 'N/A' }}</p>
                                    @if ($member->is_active == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                    @if ($member->show_on_mobile == 1)
                                        <span class="badge badge-info">Mobile Visible</span>
                                    @else
                                        <span class="badge badge-warning">Mobile Hidden</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-right">
                            <a href="{{ route('admin.edit.ready.member', base64_encode($member->id)) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Member
                            </a>
                        </div>
                    </div>

                    <!-- MAIN TABS -->
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#details">Profile Details</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#gallery">Gallery Images</a></li>
                    </ul>

                    <div class="tab-content mt-4">

                        <!-- Profile Details -->
                        <div class="tab-pane fade show active" id="details">
                            <div class="row">
                                @foreach([
                                    'Member Number' => $member->member_number,
                                    'First Name' => $member->first_name,
                                    'Last Name' => $member->last_name,
                                    'Gender' => ucfirst($member->gender),
                                    'Date of Birth' => $member->dob ? \Carbon\Carbon::parse($member->dob)->format('d M Y') : '-',
                                    'Age' => $member->dob ? \Carbon\Carbon::parse($member->dob)->age : '-',
                                    'Height' => $member->height,
                                    'Weight' => $member->weight,
                                    'Body Type' => ucfirst($member->body_type),
                                    'Hair Color' => $member->hair_color,
                                    'Eye Color' => $member->eye_color,
                                    'Nationality' => $member->nationality,
                                    'Region' => $member->region,
                                    'City' => $member->city,
                                    'Zodiac Sign' => $member->zodiac_sign,
                                    'Sexual Orientation' => $member->sexual_orientation,
                                    'Status' => $member->is_active == 1 ? 'Active' : 'Inactive',
                                    'Mobile Visible' => $member->show_on_mobile == 1 ? 'Yes' : 'No',
                                ] as $label => $value)
                                <div class="col-md-4 mb-3">
                                    <label>{{ $label }}</label>
                                    <div>{{ $value ?? '-' }}</div>
                                </div>
                                @endforeach
                                
                                @if($member->about_me)
                                <div class="col-md-12 mb-3">
                                    <label>About Me</label>
                                    <div>{{ $member->about_me }}</div>
                                </div>
                                @endif
                                
                                @if($member->about_match)
                                <div class="col-md-12 mb-3">
                                    <label>My Perfect Match</label>
                                    <div>{{ $member->about_match }}</div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Gallery Images -->
                        <div class="tab-pane fade" id="gallery">
                            <div class="mb-3">
                                <form id="galleryUploadForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="gallery_images">Upload Gallery Images</label>
                                        <input type="file" name="gallery_images[]" id="gallery_images" class="form-control" multiple accept="image/*">
                                        <small class="form-text text-muted">You can select multiple images (JPG, PNG, GIF, max 2MB each)</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Upload Images
                                    </button>
                                </form>
                            </div>
                            
                            <div class="row" id="galleryContainer">
                                @forelse($member->images as $img)
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="image-container">
                                            <img src="{{ asset($img->profile_image) }}" alt="Gallery Image">
                                            <button type="button" class="delete-image" data-id="{{ base64_encode($img->id) }}" title="Delete Image">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted">No gallery images found. Upload images to get started.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    var memberId = "{{ base64_encode($member->id) }}";
    
    // Gallery Upload
    $('#galleryUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        var files = $('#gallery_images')[0].files;
        
        if (files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please select at least one image!'
            });
            return;
        }
        
        var formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Append each file only once
        for (var i = 0; i < files.length; i++) {
            formData.append('gallery_images[]', files[i]);
        }
        
        $.ajax({
            url: "{{ url('admin/upload-ready-member-gallery') }}/" + memberId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                var errorMsg = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg
                });
            }
        });
    });
    
    // Delete Image
    $(document).on('click', '.delete-image', function() {
        var imageId = $(this).data('id');
        var imageContainer = $(this).closest('.col-md-3');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/delete-ready-member-image') }}/" + imageId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                imageContainer.fadeOut(300, function() {
                                    $(this).remove();
                                    if ($('#galleryContainer .col-md-3').length === 0) {
                                        $('#galleryContainer').html('<div class="col-12 text-center text-muted">No gallery images found. Upload images to get started.</div>');
                                    }
                                });
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Something went wrong!',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endsection
