@extends('admin::layouts.master')

@section('title', 'Nationalities Management')

@section('css')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="container-fluid  d-flex justify-content-between align-items-center">
                        <h3><i class="fas fa-flag"></i> Nationalities Management</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class=" d-flex justify-content-between align-items-center mb-3">
                            <h5>Nationalities</h5>
                            <a href="{{ route('admin.add.update.nationality') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Nationality
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle commontable" id="nationalitiesTable">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Nationality</th>
                                        <th>Created At</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nationalities as $index => $nationality)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $nationality->nationality ?? '—' }}</td>
                                            <td>{{ $nationality->created_at ? $nationality->created_at->format('d M Y') : '—' }}
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-warning btn-sm edit-btn"
                                                    href="{{ route('admin.add.update.nationality', base64_encode($nationality->id)) }}"
                                                    class="btn btn-warning btn-sm showBtn edit-btn" title="Edit">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="deleteBtn btn btn-sm btn-danger delete-btn delete-nationality"
                                                    data-id="{{ base64_encode($nationality->id) }}" title="Delete">
                                                    <i class="ph ph-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No nationalities found.
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
            // Initialize DataTables only if table exists and has data
            if ($('#nationalitiesTable').length && $('#nationalitiesTable tbody tr').length > 0) {
                $('#nationalitiesTable').DataTable();
            }

            // Delete Nationality
            $(document).on('click', '.delete-nationality', function() {
                let id = $(this).data('id');
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
                            url: "{{ url('admin/delete-nationality') }}/" + id,
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
                            error: function(xhr) {
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
