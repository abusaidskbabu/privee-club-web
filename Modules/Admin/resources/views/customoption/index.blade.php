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











        /* Modal base and fade animation */
        .modal {
            display: none;
            overflow: hidden;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            -webkit-overflow-scrolling: touch;
            outline: 0;
        }

        .modal.fade .modal-dialog {
            -webkit-transform: translate(0, -25%);
            -ms-transform: translate(0, -25%);
            -o-transform: translate(0, -25%);
            transform: translate(0, -25%);
            -webkit-transition: -webkit-transform 0.3s ease-out;
            -moz-transition: -moz-transform 0.3s ease-out;
            -o-transition: -o-transform 0.3s ease-out;
            transition: transform 0.3s ease-out;
        }

        .modal.in .modal-dialog {
            -webkit-transform: translate(0, 0);
            -ms-transform: translate(0, 0);
            -o-transform: translate(0, 0);
            transform: translate(0, 0);
        }

        /* Open modal on body */
        .modal-open {
            overflow: hidden;
        }

        .modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Backdrop (dark overlay) */
        .modal-backdrop {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            background-color: #0000005e;
        }

        .modal-backdrop.fade {
            opacity: 0;
            filter: alpha(opacity=0);
        }

        .modal-backdrop.in {
            opacity: 0.5;
            filter: alpha(opacity=50);
        }

        /* Dialog box */
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 10px;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            border: 1px solid #999;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
            box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
            background-clip: padding-box;
            outline: 0;
        }

        /* Header */
        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
            min-height: 16.42857143px;
        }

        .modal-header .close {
            margin-top: -2px;
        }

        /* Title */
        .modal-title {
            margin: 0;
            line-height: 1.42857143;
        }

        /* Body */
        .modal-body {
            position: relative;
            padding: 15px;
        }

        /* Footer */
        .modal-footer {
            padding: 15px;
            text-align: right;
            border-top: 1px solid #e5e5e5;
        }

        .modal-footer .btn+.btn {
            margin-left: 5px;
            margin-bottom: 0;
        }

        .modal-footer .btn-group .btn+.btn {
            margin-left: -1px;
        }

        .modal-footer .btn-block+.btn-block {
            margin-left: 0;
        }

        /* Scrollbar fix for body */
        .modal-scrollbar-measure {
            position: absolute;
            top: -9999px;
            width: 50px;
            height: 50px;
            overflow: scroll;
        }

        /* Responsive sizes */
        @media (min-width: 768px) {
            .modal-dialog {
                width: 600px;
                margin: 30px auto;
            }

            .modal-content {
                -webkit-box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            }

            .modal-sm {
                width: 300px;
            }
        }

        @media (min-width: 992px) {
            .modal-lg {
                width: 900px;
            }
        }

        .close {
            float: right;
            font-size: 21px;
            font-weight: bold;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: 0.2;
            filter: alpha(opacity=20);
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
            opacity: 0.5;
            filter: alpha(opacity=50);
        }

        .fade .in {
            opacity: 1;
        }

        .fade:not(.show) {
            opacity: 1;
        }
    </style>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> --}}
    @include('admin::.customoption.modal');
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header-changed">
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
                            <button class="btn btn-primary CreateModalOpen">
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
