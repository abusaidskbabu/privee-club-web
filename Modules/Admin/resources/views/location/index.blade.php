@extends('admin::layouts.master')

@section('title', 'Location Management')

@section('css')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="container-fluid d-flex justify-content-between align-items-center">
                        <h3><i class="fas fa-globe"></i> Location Management</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-body">

                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs" id="locationTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="countries-tab" data-toggle="tab" href="#countries"
                                    role="tab">
                                    Countries
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="regions-tab" data-toggle="tab" href="#regions" role="tab">
                                    Regions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="cities-tab" data-toggle="tab" href="#cities" role="tab">
                                    Cities
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="locationTabsContent">

                            <!-- Countries Tab -->
                            <div class="tab-pane fade show active" id="countries" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Countries</h5>
                                    <a href="{{ route('admin.add.update.country') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Country
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle commontable" id="countriesTable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Country</th>
                                                <th>Short Name</th>
                                                <th>Created At</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($countries as $index => $country)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $country->country ?? '—' }}</td>
                                                    <td>{{ $country->short_name ?? '—' }}</td>
                                                    <td>{{ $country->created_at ? $country->created_at->format('d M Y') : '—' }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-warning btn-sm showBtn edit-btn"
                                                            href="{{ route('admin.add.update.country', base64_encode($country->id)) }}"
                                                            class="text-warning" title="Edit">
                                                            <i class="ph ph-pencil-simple"></i>
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                            class="deleteBtn btn btn-sm btn-danger delete-btn delete-country"
                                                            data-id="{{ base64_encode($country->id) }}" title="Delete">
                                                            <i class="ph ph-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">No countries
                                                        found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Regions Tab -->
                            <div class="tab-pane fade" id="regions" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Regions</h5>
                                    <a href="{{ route('admin.add.update.region') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Region
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle commontable" id="regionsTable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Region</th>
                                                <th>Country</th>
                                                <th>Created At</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($regions as $index => $region)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $region->region ?? '—' }}</td>
                                                    <td>{{ optional($region->country)->country ?? '—' }}</td>
                                                    <td>{{ $region->created_at ? $region->created_at->format('d M Y') : '—' }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.add.update.region', base64_encode($region->id)) }}"
                                                            class="btn btn-warning btn-sm showBtn edit-btn" title="Edit">
                                                            <i class="ph ph-pencil-simple"></i>
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                            class="deleteBtn btn btn-sm btn-danger delete-btn delete-region"
                                                            data-id="{{ base64_encode($region->id) }}" title="Delete">
                                                            <i class="ph ph-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">No regions found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Cities Tab -->
                            <div class="tab-pane fade" id="cities" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Cities</h5>
                                    <a href="{{ route('admin.add.update.city') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add City
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle commontable" id="citiesTable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>City</th>
                                                <th>Region</th>
                                                <th>Country</th>
                                                <th>Created At</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($cities as $index => $city)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $city->city ?? '—' }}</td>
                                                    <td>{{ optional($city->region)->region ?? '—' }}</td>
                                                    <td>{{ optional(optional($city->region)->country)->country ?? '—' }}
                                                    </td>
                                                    <td>{{ $city->created_at ? $city->created_at->format('d M Y') : '—' }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.add.update.city', base64_encode($city->id)) }}"
                                                            class="btn btn-warning btn-sm showBtn edit-btn" title="Edit">
                                                            <i class="ph ph-pencil-simple"></i>
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                            class="deleteBtn btn btn-sm btn-danger delete-btn delete-city"
                                                            data-id="{{ base64_encode($city->id) }}" title="Delete">
                                                            <i class="ph ph-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">No cities
                                                        found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables only if tables exist and have data
            if ($('#countriesTable').length && $('#countriesTable tbody tr').length > 0) {
                $('#countriesTable').DataTable();
            }
            if ($('#regionsTable').length && $('#regionsTable tbody tr').length > 0) {
                $('#regionsTable').DataTable();
            }
            if ($('#citiesTable').length && $('#citiesTable tbody tr').length > 0) {
                $('#citiesTable').DataTable();
            }

            // Delete Country
            $(document).on('click', '.delete-country', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This country will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.country.delete', ':id') }}".replace(
                                ':id', id),
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    $('.delete-country[data-id="' + id + '"]').closest(
                                        'tr').fadeOut();
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });

            // Delete Region
            $(document).on('click', '.delete-region', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This region will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.region.delete', ':id') }}".replace(':id',
                                id),
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    $('.delete-region[data-id="' + id + '"]').closest(
                                        'tr').fadeOut();
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });

            // Delete City
            $(document).on('click', '.delete-city', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This city will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.city.delete', ':id') }}".replace(':id',
                                id),
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    $('.delete-city[data-id="' + id + '"]').closest(
                                        'tr').fadeOut();
                                } else {
                                    Swal.fire('Sorry!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Sorry!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

@endsection
