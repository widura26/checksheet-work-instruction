@extends('layouts.app')

@section('title', 'Riwayat Update Stok Material')

@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Tambah Material Baru Atau Tambah Stok
                    </h5>
                    <small class="text-body-secondary">semua aktivitas penambahan stok dalam sistem</small>
                </div>
                @if (Auth::user()->hasRole(['admin', 'staff']))
                    <div class="d-flex gap-2">
                        <a href="{{ route('tool.create') }}" class="btn btn-success text-white">
                            <i class="fas fa-plus me-1"></i>Tambah/Update Stok
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Custom Filters -->
            <div class="mb-4">
                <div class="row">
                    <div class="col-sm-12 col-lg-4">
                        <label class="form-label small fw-bold">Kode Material</label>
                        <input type="text" id="filter-kode-material" class="form-control"
                            placeholder="Cari kode material...">
                    </div>
                    <div class="col-sm-12 col-lg-4">
                        <label class="form-label small fw-bold">Project</label>
                        <select id="filter-project" class="form-select">
                            <option value="">Semua Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-lg-4">
                        <label class="form-label small fw-bold">Vendor</label>
                        <select id="filter-vendor" class="form-select">
                            <option value="">Semua</option>
                            <option value="INKA">INKA</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-lg-4 mt-3">
                        <label class="form-label small fw-bold">Dari Tanggal</label>
                        <input type="date" id="filter-start-date" class="form-control">
                    </div>
                    <div class="col-sm-12 col-lg-4 mt-3">
                        <label class="form-label small fw-bold">Sampai Tanggal</label>
                        <input type="date" id="filter-end-date" class="form-control">
                    </div>
                    <div class="col-sm-12 col-lg-4 mt-3">
                        <label class="form-label small fw-bold">Keterangan Vendor</label>
                        <input type="text" id="filter-keterangan-vendor" class="form-control"
                            placeholder="Cari keterangan...">
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <div>
                        <button type="button" id="btn-apply-filter" class="btn btn-primary">
                            Filter
                        </button>
                        <button type="button" id="btn-reset-filter" class="btn btn-secondary me-2">
                            Reset
                        </button>
                    </div>
                    <button type="button" id="btn-export-excel" class="btn btn-success text-white">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped" id="update-stock-tool-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Jumlah Ditambah</th>
                            <th class="text-center">Project</th>
                            <th class="text-center">Posisi</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Dokumen Pengambilan</th>
                            <th class="text-center">Nomor</th>
                            <th>Tanggal</th>
                            <th>Barang Datang Dari</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data will be populated by DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            var table = $('#update-stock-tool-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('update-stok-tool.data') }}",
                    data: function(d) {
                        d.kode_material = $('#filter-kode-material').val();
                        d.project_id = $('#filter-project').val();
                        d.start_date = $('#filter-start-date').val();
                        d.end_date = $('#filter-end-date').val();
                        d.vendor = $('#filter-vendor').val();
                        d.keterangan_vendor = $('#filter-keterangan-vendor').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tool.kode_material',
                        name: 'tool.kode_material',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'tool.deskripsi',
                        name: 'tool.deskripsi',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'qty_update',
                        name: 'qty_update',
                        className: 'text-center',
                    },
                    {
                        data: 'project',
                        name: 'project',
                        className: 'text-center',
                    },
                    {
                        data: 'ct',
                        name: 'ct',
                        className: 'text-center'
                    },
                    {
                        data: 'tipe',
                        name: 'tipe',
                        className: 'text-center'
                    },
                    {
                        data: 'dokumen_pengambilan',
                        name: 'dokumen_pengambilan',
                        className: 'text-center'
                    },
                    {
                        data: 'nomor_dokumen',
                        name: 'nomor_dokumen',
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'vendor',
                        name: 'vendor'
                    },
                    {
                        data: 'keterangan_vendor',
                        name: 'keterangan_vendor'
                    },
                ]
            });

            $('#btn-apply-filter').click(function() {
                table.draw();
            });

            $('#btn-reset-filter').click(function() {
                $('#filter-kode-material').val('');
                $('#filter-project').val('');
                $('#filter-start-date').val('');
                $('#filter-end-date').val('');
                $('#filter-vendor').val('');
                $('#filter-keterangan-vendor').val('');
                table.draw();
            });

            $('#btn-export-excel').click(function() {
                var params = {
                    kode_material: $('#filter-kode-material').val(),
                    project_id: $('#filter-project').val(),
                    start_date: $('#filter-start-date').val(),
                    end_date: $('#filter-end-date').val(),
                    vendor: $('#filter-vendor').val(),
                    keterangan_vendor: $('#filter-keterangan-vendor').val()
                };

                var url = "{{ route('update-stok-tool.export') }}?" + $.param(params);
                window.location.href = url;
            });

            // Optional: Trigger filter on enter key for text inputs
            $('#filter-kode-material, #filter-keterangan-vendor').keypress(function(e) {
                if (e.which == 13) {
                    table.draw();
                }
            });
        });
    </script>
@endpush
