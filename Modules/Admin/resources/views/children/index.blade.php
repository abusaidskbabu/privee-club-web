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

        /* .modal-backdrop {
                                                                                                                                                                opacity: 0.5;
                                                                                                                                                                filter: alpha(opacity=50);
                                                                                                                                                            }

                                                                                                                                                            .modal-backdrop {
                                                                                                                                                                background-color: #000;
                                                                                                                                                                opacity: 0.5 !important;
                                                                                                                                                            }

                                                                                                                                                            .fade:not(.show) {
                                                                                                                                                                opacity: 1;
                                                                                                                                                            } */
    </style>
    @include('admin::children.modal');
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="container-fluid  d-flex justify-content-between align-items-center">
                        <h3><i class="fas fa-flag"></i> Children Management</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class=" d-flex justify-content-between align-items-center mb-3">
                            <h5>Children</h5>
                            <button data-toggle="modal" data-target="#CreateModalOpen" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Nationality
                            </button>


                            <div class="modal fade" id="CreateModalOpen" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form id="CreateForm" action="" method="POST" enctype="multipart/form-data">

                                            @csrf
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Add New</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="server_side_error"></div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Title</label>
                                                            <input type="text" class="form-control" name="title"
                                                                placeholder="Enter title">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="modal-footer">
                                                <button type="submit" id="CreateModalSubmitBtn"
                                                    class="btn btn-sm btn-primary">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>



                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped commontable" id="ChildrenTable">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th class="action_table">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="mytable">
                                    @forelse($childrens as $index => $child)
                                        <tr class="trclass_{{ $child->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $child->title ?? '—' }}</td>

                                            <td>{{ $child->status == 1 ? 'Active' : 'Inactive' }}</td>

                                            <td class="action_table">
                                                <button data-toggle="modal" data-target="#EditModalOpen{{ $child->id }}"
                                                    class="btn btn-warning btn-sm edit_modal_show edit-btn" title="Edit">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </button>
                                                <a href="javascript:void(0)" class="deleteBtn btn btn-sm delete-btn"
                                                    data-id="{{ $child->id }}"
                                                    data-url="{{ route('admin.children.destroy', $child->id) }}"
                                                    title="Delete">
                                                    <i class="ph ph-trash"></i>
                                                </a>
                                                {{-- modal start --}}
                                                <div class="modal fade" id="EditModalOpen{{ $child->id }}" tabindex="-1"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-md">
                                                        <div class="modal-content">
                                                            <form id="EditForm{{ $child->id }}" method="POST"
                                                                enctype="multipart/form-data"
                                                                action="{{ route('admin.children.update', $child->id) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <b class="modal-title fs-5" id="exampleModalLabel">Edit
                                                                    </b>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body text-left">
                                                                    <div class="server_side_error"></div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Title</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="title"
                                                                                    value="{{ $child->title }}"
                                                                                    placeholder="Enter title">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Status</label>
                                                                                <select name="status" class="form-control">
                                                                                    <option value="1"
                                                                                        {{ $child->status == 1 ? 'selected' : '' }}>
                                                                                        Active</option>
                                                                                    <option value="0"
                                                                                        {{ $child->status == 0 ? 'selected' : '' }}>
                                                                                        Inactive</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-primary EditFormSubmitBtn"
                                                                        data-id="{{ $child->id }}"> Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- modal end --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No Children found.
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
