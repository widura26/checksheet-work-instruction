@extends('layouts.app')

@section('title', 'Daftar Kode Material')

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
            <div class="d-flex justify-content-end mb-3">
                @if (Auth::user()->hasRole('admin'))
                    <button type="button" class="btn btn-primary" data-coreui-toggle="modal"
                        data-coreui-target="#importModal">
                        Import Excel
                    </button>
                @endif
            </div>
            <div class="row mb-3">
                <div class="col-4">
                    <input type="text" class="form-control" name="kode_material_filter" id="kode_material_filter"
                        placeholder="Masukkan Kode Material">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="deskripsi_filter" id="deskripsi_filter"
                        placeholder="Masukkan Deskripsi Material">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="spesifikasi_filter" id="spesifikasi_filter"
                        placeholder="Masukkan Spesifikasi Material">
                </div>
            </div>
            <div class="mt-3 mb-3">
                <button id="filter-button" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i>
                    Filter
                </button>
                <button id="reset-filter-button" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i>
                    Reset
                </button>
            </div>
            <div class="table-responsive">
                <table id="table-kode-material" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Satuan</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            {{-- @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                                    <th class="text-center">Action</th>
                                @endif --}}
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Kode Material</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('daftar-kode-material.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih file Excel</label>
                            <input class="form-control" type="file" id="file" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
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
                ajax: {
                    url: "{{ route('daftar-kode-material.data') }}",
                    data: function(d) {
                        d.kode_material = $('#kode_material_filter').val();
                        d.deskripsi = $('#deskripsi_filter').val();
                        d.spesifikasi = $('#spesifikasi_filter').val();
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
                        data: 'satuan',
                        name: 'satuan'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'spesifikasi',
                        name: 'spesifikasi'
                    },
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
                $('#spesifikasi_filter').val('');
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
                        $.ajax({
                            url: `/daftar-kode-material/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
        });
    </script>
@endpush
