@extends('admin::layouts.master')

@section('title', 'Ready Members')

@section('css')
    <style>
        .timer {
            font-weight: bold;
            font-size: 16px;
        }

        .timer.expired {
            color: #b70000;
        }

        .timer.green {
            color: green;
        }

        .timer.orange {
            color: orange;
        }

        .timer.red {
            color: red;
        }

        .timer.expired {
            color: #b70000;
        }

        /* 🌈 Header Tabs */
        .page-header {
            background: linear-gradient(90deg, #f5f2ff, #ebe5ff);
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 12px rgba(95, 66, 170, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h3 {
            color: #5f42aa;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-header h3 i {
            color: #7b5cf0;
            font-size: 22px;
        }

        /* 📋 Table Styling */
        .table th {
            background: #f3f0ff;
            color: #4b3b93;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            border-top: none;
        }

        .table td {
            vertical-align: middle;
            font-size: 15px;
        }

        /* 🧭 Action Icons */
        .action-icons i {
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            margin: 0 5px;
        }

        .action-icons i.fa-eye {
            color: #28a745;
        }

        .action-icons i.fa-edit {
            color: #ffc107;
        }

        .action-icons i.fa-trash {
            color: #dc3545;
        }

        .action-icons i.fa-toggle-on {
            color: #28a745;
        }

        .action-icons i.fa-toggle-off {
            color: #6c757d;
        }

        .action-icons i:hover {
            transform: scale(1.2);
        }

        /* 🌟 Card Shadow */
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            border: none;
        }

        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <h3><i class="fas fa-user-plus"></i> Ready Members</h3>
                    <a href="{{ route('admin.add.ready.member') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Ready Member
                    </a>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-body">
                        <!-- Global Toggle -->
                        <div class=" d-flex justify-content-between align-items-center mb-4 p-3"
                            style="background: #f8f9fa; border-radius: 8px;">
                            <div>
                                <label class="font-weight-bold mb-0">Show Fake Users on Mobile App</label>
                                <p class="text-muted mb-0" style="font-size: 12px;">Toggle to show/hide all fake users in
                                    mobile API</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="globalMobileToggle" {{ $showOnMobile ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <!-- Bulk Actions -->
                        <div class=" d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <select id="bulkAction" class="form-control" style="width: 200px;">
                                    <option value="">Select Action</option>
                                    <option value="enable">Enable</option>
                                    <option value="disable">Disable</option>
                                    <option value="delete">Delete</option>
                                    <option value="toggle_mobile">Toggle Mobile Visibility</option>
                                </select>
                                <button type="button" class="btn btn-secondary" id="applyBulkAction"
                                    disabled>Apply</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle commontable" id="readyMembersTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" title="Select All">
                                        </th>
                                        <th>S.No</th>
                                        <th>Member Number</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Status</th>
                                        <th>Mobile Visible</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($readyMembers as $index => $member)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="member-checkbox" value="{{ $member->id }}">
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $member->member_number ?? '—' }}</td>
                                            <td>{{ $member->first_name ?? '—' }}</td>
                                            <td>{{ $member->last_name ?? '—' }}</td>
                                            <td>{{ $member->gender ?? '—' }}</td>
                                            <td>{{ $member->dob ? \Carbon\Carbon::parse($member->dob)->age : '—' }}</td>
                                            <td>
                                                @if ($member->is_active == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($member->show_on_mobile == 1)
                                                    <span class="badge badge-info">Yes</span>
                                                @else
                                                    <span class="badge badge-warning">No</span>
                                                @endif
                                            </td>
                                            <td class="text-center action-icons" style="min-width: 150px;">

                                                <a class="btn btn-warning btn-sm showBtn show-btn"
                                                    href="{{ route('admin.view.ready.member', base64_encode($member->id)) }}"
                                                    title="View">
                                                    <i class="ph ph-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.edit.ready.member', base64_encode($member->id)) }}"
                                                    class="btn btn-warning btn-sm showBtn edit-btn" title="Edit">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="btn btn-warning toggle-status toggle_button"
                                                    data-id="{{ base64_encode($member->id) }}" title="Toggle Status">
                                                    <i
                                                        class="fa {{ $member->is_active == 1 ? 'ph ph-toggle-right' : 'ph ph-toggle-left' }}"></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="deleteBtn btn btn-sm btn-danger delete-btn delete-member"
                                                    data-id="{{ base64_encode($member->id) }}" title="Delete">
                                                    <i class="ph ph-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">No ready members found.
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



    <script>
        $(document).ready(function() {
            // Initialize DataTables
            if ($('#readyMembersTable tbody tr').length > 0) {
                $('#readyMembersTable').DataTable();
            }

            // Select All checkbox
            $('#selectAll').on('change', function() {
                $('.member-checkbox').prop('checked', $(this).prop('checked'));
                updateBulkActionButton();
            });

            // Individual checkbox change
            $(document).on('change', '.member-checkbox', function() {
                var total = $('.member-checkbox').length;
                var checked = $('.member-checkbox:checked').length;
                $('#selectAll').prop('checked', total === checked);
                updateBulkActionButton();
            });

            // Update bulk action button state
            function updateBulkActionButton() {
                var checked = $('.member-checkbox:checked').length;
                $('#applyBulkAction').prop('disabled', checked === 0 || $('#bulkAction').val() === '');
            }

            $('#bulkAction').on('change', updateBulkActionButton);

            // Global Mobile Toggle
            $('#globalMobileToggle').on('change', function() {
                var showOnMobile = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.toggle.global.mobile.visibility') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        show_on_mobile: showOnMobile
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong!'
                        });
                    }
                });
            });

            // Bulk Action
            $('#applyBulkAction').on('click', function() {
                var action = $('#bulkAction').val();
                var ids = [];

                $('.member-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Please select at least one member!'
                    });
                    return;
                }

                if (!action) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Please select an action!'
                    });
                    return;
                }

                var confirmText = 'Are you sure you want to ' + action + ' ' + ids.length + ' member(s)?';
                if (action === 'delete') {
                    confirmText = 'Are you sure you want to delete ' + ids.length +
                        ' member(s)? This action cannot be undone!';
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.bulk.action.ready.members') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                action: action,
                                ids: ids
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Something went wrong!'
                                });
                            }
                        });
                    }
                });
            });

            // Toggle Status
            $(document).on('click', '.toggle-status', function() {
                var id = $(this).data('id');
                var icon = $(this).find('i');

                $.ajax({
                    url: "{{ url('admin/toggle-ready-member-status') }}/" + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong!'
                        });
                    }
                });
            });

            // Delete Member
            $(document).on('click', '.delete-member', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/delete-ready-member') }}/" + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong!',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Show success/error messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}'
                });
            @endif
        });
    </script>
@endsection
