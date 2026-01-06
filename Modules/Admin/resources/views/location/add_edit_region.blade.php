@extends('admin::layouts.master')

@section('title', isset($region) && $region->id ? 'Update Region' : 'Add Region')

@section('css')
@endsection

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="page-header">
        <div class="container-fluid  d-flex justify-content-between align-items-center">
          <h3><i class="fas fa-globe"></i> {{ isset($region) && $region->id ? 'Update Region' : 'Add Region' }}</h3>
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
              <form action="{{ route('admin.add.update.region', isset($region) && $region->id ? base64_encode($region->id) : '') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                  <label for="country_id">Country <span class="text-danger">*</span></label>
                  <select name="country_id" class="form-control" required>
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                      <option value="{{ $country->id }}" 
                        {{ old('country_id', (isset($region) && $region->id ? $region->country_id : '')) == $country->id ? 'selected' : '' }}>
                        {{ $country->country }}
                      </option>
                    @endforeach
                  </select>
                  @error('country_id')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <div class="form-group mb-3">
                  <label for="region">Region <span class="text-danger">*</span></label>
                  <input type="text" name="region" class="form-control"
                    value="{{ old('region', isset($region) && $region->id ? $region->region : '') }}"
                    placeholder="Enter region name" required>
                  @error('region')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <button type="submit" class="btn btn-success">
                  {{ isset($region) && $region->id ? 'Update Region' : 'Add Region' }}
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
