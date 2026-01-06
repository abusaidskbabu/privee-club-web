@extends('admin::layouts.master')

@section('title', isset($country) && $country->id ? 'Update Country' : 'Add Country')

@section('css')
@endsection

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="page-header">
        <div class="container-fluid  d-flex justify-content-between align-items-center">
          <h3><i class="fas fa-globe"></i> {{ isset($country) && $country->id ? 'Update Country' : 'Add Country' }}</h3>
          <button class="btn btn-primary">
            <a href="{{ route('admin.location.management') }}" class="back-btn" style="color:black; text-decoration: none;">← Back</a>
          </button>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="tab-content" id="genderTabsContent">
        <div class="tab-pane fade show active" id="male" role="tabpanel" aria-labelledby="male-tab">
          <div class="card mt-3">
            <div class="card-body table-responsive">
              <form action="{{ route('admin.add.update.country', isset($country) && $country->id ? base64_encode($country->id) : '') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                  <label for="country">Country <span class="text-danger">*</span></label>
                  <input type="text" name="country" class="form-control"
                    value="{{ old('country', isset($country) && $country->id ? $country->country : '') }}"
                    placeholder="Enter country name" required>
                  @error('country')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <div class="form-group mb-3">
                  <label for="short_name">Short Name</label>
                  <input type="text" name="short_name" class="form-control"
                    value="{{ old('short_name', isset($country) && $country->id ? $country->short_name : '') }}"
                    placeholder="Enter short name (e.g., US, UK)" maxlength="10">
                  @error('short_name')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <button type="submit" class="btn btn-success">
                  {{ isset($country) && $country->id ? 'Update Country' : 'Add Country' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
