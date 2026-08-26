@extends('layouts.app')

@section('title', 'Data Material')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" id="kode_material_filter" class="form-control" placeholder="Filter Kode Material">
                </div>
                <div class="col-md-6">
                    <input type="text" id="deskripsi_filter" class="form-control" placeholder="Filter Deskripsi">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <button id="filter-button" class="btn btn-primary">
                        <svg class="icon me-1"><use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-filter') }}"></use></svg>
                        Filter
                    </button>
                    <button id="reset-filter-button" class="btn btn-secondary">
                        <svg class="icon me-1"><use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-sync') }}"></use></svg>
                        Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="table-kode-material" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            <th>Satuan</th>
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
            var table = $('#table-kode-material').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                info: false,
                ajax: {
                    url: "{{ route('kode-material.data') }}",
                    data: function(d) {
                        d.kode_material = $('#kode_material_filter').val();
                        d.deskripsi = $('#deskripsi_filter').val();
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
                        data: 'kode_material',
                        name: 'kode_material'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'spesifikasi',
                        name: 'spesifikasi'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
            });

            $('#filter-button').click(function() {
                table.ajax.reload();
            });

            $('#reset-filter-button').click(function() {
                $('#kode_material_filter').val('');
                $('#deskripsi_filter').val('');
                table.ajax.reload();
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
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('kode-material.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Terhapus!',
                                    response.message ?? 'Data berhasil dihapus.',
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function() {
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
        });
    </script>
@endpush
