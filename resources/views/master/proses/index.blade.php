@extends('layouts.app')

@section('title', 'Kode Proses')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }

        #table-proses {
            width: 100% !important;
        }

        .icon {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Kode Proses</h5>
            </div>
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
                    <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 5h18v2H3zM3 11h18v2H3zM3 17h18v2H3z"></path></svg>
                    Filter
                </button>
                <button id="reset-filter-button" class="btn btn-secondary">
                    <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 5V1L7 6l5 5V7a5 5 0 11-5 5H5a7 7 0 107-7z"></path></svg>
                    Reset
                </button>
            </div>
            <div class="table-responsive">
                <table id="table-proses" class="table table-bordered table-hover" style="width: 100%;">
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
                processing: false,
                serverSide: true,
                responsive: true,
                info: false,
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
