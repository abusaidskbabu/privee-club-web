@extends('admin::layouts.master')

@section('title', 'Children Management')
@section('content')
    <style>
        .mytable td {
            text-align: left;
        }

        table.dataTable td {
            text-align: left;
        }

        .action_table,
        .action_table a {
            text-align: right !important;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    @include('admin::.customoption.modal');
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="container-fluid">
                        <h3><i class="fas fa-flag"></i> Custom Options Management</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class=" d-flex justify-content-between align-items-center mb-3">
                            <h5>Custom Options</h5>
                            <button data-toggle="modal" data-target="#CreateModalOpen" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Option
                            </button>




                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped commontable" id="ChildrenTable">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Name</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                        <th class="action_table">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="mytable">
                                    @forelse($customoptions as $index => $option)
                                        <tr class="trclass_{{ $option->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $option->name ?? '—' }}</td>
                                            <td>{{ $option->value ?? '—' }}</td>

                                            <td>{{ $option->status == 1 ? 'Active' : 'Inactive' }}</td>

                                            <td class="action_table">
                                                <button class="btn btn-warning btn-sm edit_modal_show edit-btn"
                                                    data-url="/admin/custom-options/{{ $option->id }}/edit"
                                                    title="Edit">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </button>
                                                <a href="javascript:void(0)" class="deleteBtn btn btn-sm delete-btn"
                                                    data-id="{{ $option->id }}"
                                                    data-url="{{ route('admin.custom-options.destroy', $option->id) }}"
                                                    title="Delete">
                                                    <i class="ph ph-trash"></i>
                                                </a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No Custom options
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
