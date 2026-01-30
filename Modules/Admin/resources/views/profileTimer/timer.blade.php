@extends('admin::layouts.master')
@section('content')
    <style>
        /* Improved input styling */
        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 15px;
            transition: 0.2s ease;
        }

        .form-control:focus {
            border-color: #FFD0E7;
            box-shadow: 0 0 5px #FFD0E7;
            outline: none;
        }

        /* Updated button styling */
        button.btnprimary {
            border: none;
            padding: 8px 25px;
            background: #FFD0E7;
            color: #4a276f;
            font-size: 15px;
            font-weight: 700;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        button.btnprimary:hover {
            background: #ffb6d6;
            color: #3a1e58;
        }
    </style>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                                @endif
                                <form method="POST" action="{{ route('admin.update-time') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="time" class="form-label">Rating Window (in Hours)</label>
                                            <input type="text" id="time" name="time" class="form-control"
                                                placeholder="Enter time (e.g., 1 hour)" value="{{ $time?->time }}">
                                            @if ($errors->has('time'))
                                                <div class="text-danger">{{ $errors->first('time') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="stickness_level" class="form-label">Stickness Level</label>
                                            <select id="stickness_level" name="stickness_level"
                                                class="form-control form-select">
                                                <option value="1.0"
                                                    {{ isset($time) && $time->stickness_level == 1.0 ? 'selected' : '' }}>
                                                    Lenient</option>
                                                <option value="0.5"
                                                    {{ isset($time) && $time->stickness_level == 0.5 ? 'selected' : '' }}>
                                                    Moderate</option>
                                                <option value="0.33"
                                                    {{ isset($time) && $time->stickness_level == 0.33 ? 'selected' : '' }}>
                                                    Strict</option>
                                            </select>
                                            @if ($errors->has('stickness_level'))
                                                <div class="text-danger">{{ $errors->first('stickness_level') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-rotate"></i> Update
                                    </button>
                                </form>

                            </div>
                        </div>

                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </section>
    </div>
@endsection
