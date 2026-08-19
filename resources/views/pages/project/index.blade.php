@extends('layouts.app')

@section('title', 'Daftar Project')

@push('css')
    <style>
        .status-badge {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
            color: white;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Daftar Project</h5>
                <small class="text-body-secondary">Halaman ini menampilkan daftar semua project</small>
            </div>
            @role('admin')
                <a class="btn btn-primary" href="{{ route('project.create') }}">
                    <svg class="icon me-2">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-plus') }}"></use>
                    </svg>
                    Tambah Project
                </a>
            @endrole
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table-project" class="table table-striped table-hover" style="width:100%">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Project ID</th>
                            <th>Nama Project</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
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
            var table = $('#table-project').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('project.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'project_id',
                        name: 'project_id'
                    },
                    {
                        data: 'nama_project',
                        name: 'nama_project'
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(data, type, row) {
                            const statusLabels = {
                                'planning': 'Perencanaan',
                                'ongoing': 'Berlangsung',
                                'completed': 'Selesai',
                                'cancelled': 'Dibatalkan'
                            };

                            const statusClasses = {
                                'planning': 'bg-secondary',
                                'ongoing': 'bg-primary',
                                'completed': 'bg-success',
                                'cancelled': 'bg-danger'
                            };

                            const label = statusLabels[data] || 'Tidak Diketahui';
                            const className = statusClasses[data] || 'bg-secondary';

                            return '<span class="badge ' + className + '">' + label + '</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            @role('admin')
                $(document).on('click', '.deleteButton', function() {
                    const id = $(this).data('id');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data project ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menghapus...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: `/project/${id}`,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Project berhasil dihapus.',
                                        'success'
                                    );
                                    table.ajax.reload();
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Gagal!',
                                        'Terjadi kesalahan saat menghapus project.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            @endrole
        });
    </script>
@endpush
