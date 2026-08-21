@extends('layouts.app')

@section('title', 'Manajemen SPM')

@section('content')

    <div class="card mb-3">
        <div class="card-header">
            Filter Data
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filter_no_spm">No. SPM</label>
                    <input type="text" id="filter_no_spm" class="form-control" placeholder="Filter No. SPM">
                </div>
                <div class="col-md-3">
                    <label for="filter_project">Project</label>
                    <select id="filter_project" class="form-select">
                        <option value="">Semua Project</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_tanggal_spm">Tanggal SPM</label>
                    <input type="date" id="filter_tanggal_spm" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="filter_status">Status</label>
                    <select id="filter_status" class="form-select">
                        <option value="">Semua Status</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button id="filter-button" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i>
                    Filter
                </button>
                <button id="reset-filter-button" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header">
            Menampilkan Seluruh Surat Permintaan Material (SPM)
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex align-items-center">
                </div>
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                    <a href="{{ route('spm.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>
                        Buat SPM
                    </a>
                @endif
            </div>

            <div class="table-responsive">
                <table id="table-spm" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>No. SPM</th>
                            <th>Project</th>
                            <th>Kepada</th>
                            <th>Tanggal SPM</th>
                            <th>Status</th>
                            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                                <th class="text-center">Action</th>
                            @endif
                            <th>Kedatangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            // Populate Project Filter
            $.get('{{ route('spm.getProjects') }}', function(data) {
                const projectFilter = $('#filter_project');
                projectFilter.empty();
                projectFilter.append('<option value="">Semua Project</option>');
                data.forEach(function(project) {
                    projectFilter.append(new Option(project.name, project.project_id));
                });
            });

            // Populate Status Filter
            $.get('{{ route('spm.getStatuses') }}', function(data) {
                const statusFilter = $('#filter_status');
                statusFilter.empty();
                statusFilter.append('<option value="">Semua Status</option>');
                data.forEach(function(status) {
                    statusFilter.append(new Option(status.name, status.value));
                });
            });

            var table = $('#table-spm').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('spm.getData') }}",
                    data: function(d) {
                        d.no_spm = $('#filter_no_spm').val();
                        d.project_id = $('#filter_project').val();
                        d.tanggal_spm = $('#filter_tanggal_spm').val();
                        d.status = $('#filter_status').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'no_spm',
                        name: 'no_spm'
                    },
                    {
                        data: 'project_id',
                        name: 'project_id'
                    },
                    {
                        data: 'kepada',
                        name: 'kepada'
                    },
                    {
                        data: 'tanggal_spm',
                        name: 'tanggal_spm'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',

                        },
                    @endif {
                        data: 'kedatangan',
                        name: 'kedatangan'
                    },
                ],
                autoWidth: false,
            });

            $('#filter-button').click(function() {
                table.ajax.reload();
            });

            $('#reset-filter-button').click(function() {
                $('#filter_no_spm').val('');
                $('#filter_project').val('');
                $('#filter_tanggal_spm').val('');
                $('#filter_status').val('');
                table.ajax.reload();
            });

            $(document).on('click', '.uploadButton', function() {
                const id = $(this).data('id');
                $('#spm_id').val(id);
            });

            $(document).on('click', '.deleteButton', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/spm/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Gagal!',
                                    'Terjadi kesalahan saat menghapus data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success'
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    icon: 'error'
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    title: 'Gagal!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    icon: 'error'
                });
            @endif
        });
    </script>
@endpush
