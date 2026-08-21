@extends('layouts.app')

@section('title', 'Daftar Distribusi')

@push('css')
@endpush

@section('content')
    @if (session('success'))
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Distribusi</h5>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="type-filter" class="form-label">Tipe</label>
                        <select id="type-filter" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="peminjaman">Peminjaman</option>
                            <option value="penyerahan">Penyerahan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="departemen-filter" class="form-label">Departemen</label>
                        <select id="departemen-filter" class="form-select select2">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemens as $departemen)
                                <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="bagian-filter" class="form-label">Bagian</label>
                        <select id="bagian-filter" class="form-select select2">
                            <option value="">Semua Bagian</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="unit-filter" class="form-label">Unit</label>
                        <select id="unit-filter" class="form-select select2">
                            <option value="">Semua Unit</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="start-date-filter" class="form-label">Tanggal Distribusi Dari</label>
                        <input type="date" id="start-date-filter" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="end-date-filter" class="form-label">Sampai Tanggal</label>
                        <input type="date" id="end-date-filter" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label for="project-filter" class="form-label">Project</label>
                        <select id="project-filter" class="form-select select2">
                            <option value="">Semua Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="project-filter" class="form-label">Kode Material</label>
                        <input type="text" class="form-control" name="kode-material-filter" id="kode-material-filter">
                    </div>
                    <div class="col-md-4">
                        <label for="project-filter" class="form-label">Kategori Distribusi</label>
                        <select name="kategori-filter" id="kategori-filter" class="form-control">
                            <option value="">Semua Kategori</option>
                            <option value="tool">Tool</option>
                            <option value="consumable">Consumable</option>
                            <option value="apd">Apd</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="button" id="filter-btn" class="btn btn-primary">Filter</button>
                    <button type="button" id="reset-btn" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Distribusi</h5>
            <small class="text-body-secondary">Halaman ini menampilkan data distribusi tools (penyerahan dan peminjaman)
                pada
                central tools</small>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end gap-2 mb-3">
                <button id="export-excel-btn" class="btn btn-success text-white">
                    <svg class="icon">
                        <use
                            xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-data-transfer-down') }}">
                        </use>
                    </svg>
                    Export Excel
                </button>
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                    <a href="{{ route('distribusi.peminjaman.create') }}" class="btn btn-primary text-white">
                        Peminjaman
                    </a>
                    <a href="{{ route('distribusi.penyerahan.create') }}" class="btn btn-info text-white">
                        Penyerahan
                    </a>
                @endif
            </div>
            <div class="row mb-3">
                @if (Auth::user()->hasRole('admin'))
                    <form action="{{ route('import-distribusi') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls">
                            <button type="submit" class="btn btn-success text-white">Import Riwayat Distribusi</button>
                        </div>
                    </form>
                @endif
            </div>
            {{-- Tabel error import --}}
            @if (session('import_errors') && count(session('import_errors')) > 0)
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <strong>Data Gagal Di-import</strong>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="table-danger">
                                <tr>
                                    <th style="width: 120px;">Baris Di Excel</th>
                                    <th style="width: 200px;">Kode Material</th>
                                    <th>Pesan Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (session('import_errors') as $error)
                                    <tr>
                                        <td class="text-center">
                                            {{ $error['row'] }}
                                        </td>
                                        <td>
                                            {{ $error['kode_material'] ?? '-' }}
                                        </td>
                                        <td class="text-danger">
                                            {{ $error['error'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        <small class="text-muted">
                            Perbaiki data pada file Excel sesuai baris di atas, lalu upload ulang.
                        </small>
                    </div>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="distribusi-table">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Staff</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Penerima</th>
                            <th>NIP</th>
                            <th>Departemen</th>
                            <th>Bagian</th>
                            <th>Unit</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh DataTables -->
                    </tbody>
                </table>
                <div class="modal fade" id="modalUploadBast" tabindex="-1" aria-labelledby="modalUploadBastLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalUploadBastLabel">
                                    Upload File BAST
                                </h5>
                                <button type="button" class="btn-close" data-coreui-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('distribusi.upload-bast') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="distribusi_tool_id" id="distribusi_tool_id">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            File BAST
                                        </label>
                                        <input type="file" name="file_bast" class="form-control"
                                            accept=".pdf,.jpg,.jpeg,.png" required>
                                        <div class="form-text">
                                            Format: PDF / JPG / PNG (Max 2MB)
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-coreui-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i> Upload
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-upload-bast');
                if (!btn) return;
                document.getElementById('distribusi_tool_id').value = btn.dataset.id;
            });

            function load_data(filters = {}) {
                $('#distribusi-table').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    order: [
                        [1, 'desc']
                    ],
                    ajax: {
                        url: "{{ route('distribusi.datatable') }}",
                        data: filters
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'tanggal_pinjam_diserahkan',
                            name: 'distribusiTool.tanggal_pinjam_diserahkan'
                        },
                        {
                            data: 'staff',
                            name: 'staff',
                            searchable: true,
                        },
                        {
                            data: 'tool.kode_material',
                            name: 'tool.kode_material',
                            orderable: false,
                            searchable: true,
                        },
                        {
                            data: 'tool.deskripsi',
                            name: 'tool.deskripsi',
                            orderable: false,
                        },
                        {
                            data: 'tool.spesifikasi',
                            name: 'tool.spesifikasi',
                            orderable: false,
                        },
                        {
                            data: 'satuan',
                            name: 'satuan',
                            orderable: false,
                        },
                        {
                            data: 'jumlah_distribusi',
                            name: 'jumlah_distribusi'
                        },
                        {
                            data: 'kategori',
                            name: 'distribusiTool.kategori'
                        },
                        {
                            data: 'type',
                            name: 'distribusiTool.type'
                        },
                        {
                            data: 'nama_peminjam',
                            name: 'nama_peminjam',
                            orderable: false
                        },
                        {
                            data: 'nip',
                            name: 'distribusiTool.nip',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'departemen',
                            name: 'departemen',
                            orderable: false
                        },
                        {
                            data: 'bagian',
                            name: 'bagian'
                        },
                        {
                            data: 'unit',
                            name: 'unit'
                        },
                        {
                            data: 'project',
                            name: 'project',
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',

                        },
                    ],
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: true,
                });
            }

            load_data();

            $('#filter-btn').click(function() {
                const filters = {
                    type: $('#type-filter').val(),
                    departemen_id: $('#departemen-filter').val(),
                    project_id: $('#project-filter').val(),
                    bagian_id: $('#bagian-filter').val(),
                    unit_id: $('#unit-filter').val(),
                    start_date: $('#start-date-filter').val(),
                    end_date: $('#end-date-filter').val(),
                    kode_material: $('#kode-material-filter').val(),
                    kategori: $('#kategori-filter').val(),
                };

                load_data(filters);
            });

            $('#reset-btn').click(function() {
                $('#filter-form')[0].reset();
                load_data();
            });

            $('#departemen-filter').on('change', function() {
                var departemenId = $(this).val();
                $('#bagian-filter').empty().append('<option value="">Semua Bagian</option>');
                $('#unit-filter').empty().append('<option value="">Semua Unit</option>');
                if (departemenId) {
                    $.ajax({
                        url: "{{ route('distribusi.api.bagian', ['departemen_id' => ':departemenId']) }}"
                            .replace(':departemenId', departemenId),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#bagian-filter').append('<option value="' + value
                                    .id + '">' + value.nama_bagian + '</option>');
                            });
                        }
                    });
                }
            });

            $('#bagian-filter').on('change', function() {
                var bagianId = $(this).val();
                $('#unit-filter').empty().append('<option value="">Semua Unit</option>');
                if (bagianId) {
                    $.ajax({
                        url: "{{ route('distribusi.api.unit', ['bagian_id' => ':bagianId']) }}"
                            .replace(':bagianId', bagianId),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#unit-filter').append('<option value="' + value.id +
                                    '">' + value.nama_unit + '</option>');
                            });
                        }
                    });
                }
            });

            $('#export-excel-btn').on('click', function(e) {
                e.preventDefault();
                const params = new URLSearchParams({
                    type: $('#type-filter').val(),
                    departemen_id: $('#departemen-filter').val(),
                    project_id: $('#project-filter').val(),
                    bagian_id: $('#bagian-filter').val(),
                    unit_id: $('#unit-filter').val(),
                    start_date: $('#start-date-filter').val(),
                    end_date: $('#end-date-filter').val(),
                    kode_material: $('#kode-material-filter').val(),
                    kategori: $('#kategori-filter').val(),
                });
                window.location.href = "{{ route('distribusi.export') }}?" + params.toString();
            });

        });
    </script>
@endpush
