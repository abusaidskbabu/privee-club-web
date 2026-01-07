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
                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                                @endif
                                <form method="POST" action={{ route('admin.update-time') }} enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-5 mb-2">
                                            <label for="personal_trait" class="form-label">
                                                Rating Window (in Hours)
                                            </label>
                                            <input type="text" class="form-control"
                                                placeholder="Enter timer (e.g., 1 hour)" name="timer"
                                                value="{{ old('time', $timer->time ?? '') }}">
                                            @if ($errors->has('timer'))
                                                <span class="text-danger">{{ $errors->first('timer') }}</span>
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label for=""> Update</label>
                                            <button type="submit" class="btnprimary">
                                                <i class="fa-solid fa-rotate"></i> Update
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
