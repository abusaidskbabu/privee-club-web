@extends('admin::layouts.master')

@section('title', isset($city) && $city->id ? 'Update City' : 'Add City')

@section('css')
@endsection

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="page-header">
        <div class="container-fluid  d-flex justify-content-between align-items-center">
          <h3><i class="fas fa-globe"></i> {{ isset($city) && $city->id ? 'Update City' : 'Add City' }}</h3>
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
              <form action="{{ route('admin.add.update.city', isset($city) && $city->id ? base64_encode($city->id) : '') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                  <label for="region_id">Region <span class="text-danger">*</span></label>
                  <select name="region_id" class="form-control" required>
                    <option value="">Select Region</option>
                    @foreach($regions as $region)
                      <option value="{{ $region->id }}" 
                        {{ old('region_id', (isset($city) && $city->id ? $city->region_id : '')) == $region->id ? 'selected' : '' }}>
                        {{ $region->region }} 
                        @if($region->country)
                          ({{ $region->country->country }})
                        @endif
                      </option>
                    @endforeach
                  </select>
                  @error('region_id')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <div class="form-group mb-3">
                  <label for="city">City <span class="text-danger">*</span></label>
                  <input type="text" name="city" class="form-control"
                    value="{{ old('city', isset($city) && $city->id ? $city->city : '') }}"
                    placeholder="Enter city name" required>
                  @error('city')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <button type="submit" class="btn btn-success">
                  {{ isset($city) && $city->id ? 'Update City' : 'Add City' }}
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
