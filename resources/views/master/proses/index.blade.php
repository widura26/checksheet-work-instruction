@extends('layouts.app')

@section('title', 'Proses')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Data Proses</h5>
            <small class="text-body-secondary">Halaman ini menampilkan data master proses</small>
        </div>
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
                <div class="col-4">
                    <input type="text" class="form-control" name="kode_proses_filter" id="kode_proses_filter"
                        placeholder="Masukkan Kode Proses">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="nama_filter" id="nama_filter"
                        placeholder="Masukkan Nama Proses">
                </div>
                <div class="col-4">
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
                <table id="table-proses" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Proses</th>
                            <th>Nama Proses</th>
                            <th>ID Kelompok</th>
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
            var table = $('#table-proses').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('processes.data') }}",
                    data: function(d) {
                        d.kode_proses = $('#kode_proses_filter').val();
                        d.nama = $('#nama_filter').val();
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
                        data: 'kode_proses',
                        name: 'kode_proses'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'id_kel',
                        name: 'id_kel'
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
                $('#kode_proses_filter').val('');
                $('#nama_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
