@extends('admin::layouts.master')

@section('title', 'Hear About Us Management')
@section('content')
    <link rel="stylesheet" href="{{ url('assets/css/modal.css') }}?v={{ time() }}">
    @include('admin::hearaboutus.modal');
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header-changed">
                    <div class="container-fluid">
                        <h3><i class="fas fa-flag"></i> Hear About Us Management</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class=" d-flex justify-content-between align-items-center mb-3">
                            <h5>Hear About Us</h5>
                            <button class="btn btn-primary CreateModalOpenBtn">
                                <i class="fas fa-plus"></i> Add New
                            </button>




                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped commontable" id="ChildrenTable">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Platform</th>
                                        <th class="action_table">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="mytable">
                                    @forelse($hearaboutus as $index => $option)
                                        <tr class="trclass_{{ $option->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $option->platform ?? '—' }}</td>

                                            <td class="action_table">
                                                <button class="btn btn-warning btn-sm edit_modal_show edit-btn"
                                                    data-url="/admin/hear-about-us/{{ $option->id }}/edit" title="Edit">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </button>
                                                <a href="javascript:void(0)" class="deleteBtn btn btn-sm delete-btn"
                                                    data-id="{{ $option->id }}"
                                                    data-url="{{ route('admin.hear-about-us.destroy', $option->id) }}"
                                                    title="Delete">
                                                    <i class="ph ph-trash"></i>
                                                </a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No Hear About Us
                                                found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
