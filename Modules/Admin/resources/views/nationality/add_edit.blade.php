@extends('admin::layouts.master')

@section('title', isset($nationality) && $nationality->id ? 'Update Nationality' : 'Add Nationality')

@section('css')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="container-fluid  d-flex justify-content-between align-items-center">
                        <h3><i class="fas fa-flag"></i>
                            {{ isset($nationality) && $nationality->id ? 'Update Nationality' : 'Add Nationality' }}</h3>
                        <button class="btn btn-primary">
                            <a href="{{ route('admin.nationalities.management') }}" class="back-btn"
                                style="color:black; text-decoration: none;">← Back</a>
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
                                <div class="form-group">
                                    <label>Select Language</label>
                                    <ul class="navbar-nav">
                                        <li class="nav-item">
                                            <form action="{{ url('/admin/change-language') }}" method="POST">
                                                @csrf
                                                <select class="form-control form-control-sm language-select">
                                                    <option value="da"
                                                        {{ Session::get('admin_language') == 'da' ? 'selected' : '' }}>
                                                        Dansk
                                                    </option>
                                                    <option value="en"
                                                        {{ Session::get('admin_language') == 'en' ? 'selected' : '' }}>
                                                        English
                                                    </option>
                                                </select>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                <form
                                    action="{{ route('admin.add.update.nationality', isset($nationality) && $nationality->id ? base64_encode($nationality->id) : '') }}"
                                    method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="nationality">Nationality <span class="text-danger">*</span></label>
                                        <input type="text" name="nationality" class="form-control"
                                            value="{{ old('nationality', isset($nationality) && $nationality->id ? $nationality->nationality : '') }}"
                                            placeholder="Enter nationality name" required>
                                        @error('nationality')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-success">
                                        {{ isset($nationality) && $nationality->id ? 'Update Nationality' : 'Add Nationality' }}
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
