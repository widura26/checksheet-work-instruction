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
                    <input type="text" id="id_wi_trn_bpm_filter" class="form-control" placeholder="Filter Kode Material">
                </div>
                <div class="col-md-6">
                    <input type="text" id="spec_filter" class="form-control" placeholder="Filter Spesifikasi">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
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

            <div class="table-responsive">
                <table id="table-kode-material" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Spesifikasi</th>
                            <th>Keterangan</th>
                            <th>User</th>
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
                        d.id_wi_trn_bpm = $('#id_wi_trn_bpm_filter').val();
                        d.spec = $('#spec_filter').val();
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
                        data: 'id_wi_trn_bpm',
                        name: 'id_wi_trn_bpm'
                    },
                    {
                        data: 'spec',
                        name: 'spec'
                    },
                    {
                        data: 'ket',
                        name: 'ket'
                    },
                    {
                        data: 'user',
                        name: 'user',
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
                $('#id_wi_trn_bpm_filter').val('');
                $('#spec_filter').val('');
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
