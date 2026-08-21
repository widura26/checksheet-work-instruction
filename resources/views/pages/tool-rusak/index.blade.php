@extends('layouts.app')

@section('content')
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Daftar Tool Rusak</h4>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filter-status" class="form-label">Status Perbaikan</label>
                            <select id="filter-status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Menunggu SPR">Menunggu SPR</option>
                                <option value="Sedang Diperbaiki">Sedang Diperbaiki</option>
                                <option value="Sudah Diperbaiki">Sudah Diperbaiki</option>
                                <option value="Tidak Dapat Diperbaiki">Tidak Dapat Diperbaiki</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-kode-material" class="form-label">Kode Material</label>
                            <input type="text" id="filter-kode-material" class="form-control"
                                placeholder="Cari kode material...">
                        </div>
                        <div class="col-md-3">
                            <label for="filter-tanggal-dari" class="form-label">Tanggal SPR Dari</label>
                            <input type="date" id="filter-tanggal-dari" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="filter-tanggal-sampai" class="form-label">Tanggal SPR Sampai</label>
                            <input type="date" id="filter-tanggal-sampai" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" id="btn-filter" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" id="btn-reset" class="btn btn-secondary">
                                <i class="fas fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover" id="tool-rusak-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Material</th>
                                    <th>Deskripsi</th>
                                    <th>Deskripsi Kerusakan</th>
                                    <th>Project</th>
                                    <th>Lokasi Tool</th>
                                    <th>Tanggal SPR</th>
                                    <th>Status Perbaikan</th>
                                    <th>File SPR</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach ($detailKerusakanTools as $tool)
        @if (Auth::user()->hasRole(['admin', 'staff']))
            <!-- Modal -->
            <div class="modal fade" id="uploadModal{{ $tool->id }}" tabindex="-1" role="dialog"
                aria-labelledby="uploadModalLabel{{ $tool->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel{{ $tool->id }}">
                                Upload SPR - {{ $tool->detailDaftarDistribusiTool->daftarDistribusiTool->tool->deskripsi }}
                            </h5>
                            <button type="button" class="btn-close" data-coreui-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('tool-rusak.upload-spr', $tool->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="file_spr">Nomor SPR</label>
                                    <input type="text" class="form-control" name="no_spr" required>
                                </div>
                                <div class="mb-3">
                                    <label for="file_spr">File SPR</label>
                                    <input type="file" class="form-control" id="file_spr" name="file_spr" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('css')
    <style>
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .row {
            margin-bottom: 0.5rem;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@push('js')
    <script type="module">
        $(function() {
            var table = $('#tool-rusak-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('tool-rusak.datatable') }}',
                    data: function(d) {
                        d.status_filter = $('#filter-status').val();
                        d.kode_material_filter = $('#filter-kode-material').val();
                        d.tanggal_dari_filter = $('#filter-tanggal-dari').val();
                        d.tanggal_sampai_filter = $('#filter-tanggal-sampai').val();
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
                        data: 'tool.kode_material',
                        name: 'tool.kode_material'
                    },
                    {
                        data: 'tool.deskripsi',
                        name: 'tool.deskripsi'
                    },
                    {
                        data: 'deskripsi_kerusakan',
                        name: 'deskripsi_kerusakan'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi'
                    },
                    {
                        data: 'tanggal_spr',
                        name: 'tanggal_spr',
                        className: 'text-center'
                    },
                    {
                        data: 'status_perbaikan',
                        name: 'status_perbaikan',
                        className: 'text-center'
                    },
                    {
                        data: 'file_spr',
                        name: 'file_spr',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    @if (Auth::user()->hasRole(['admin', 'staff']))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    @endif
                ],
                autoWidth: false,
            });

            // Filter button click event
            $('#btn-filter').on('click', function() {
                table.draw();
            });

            // Reset button click event
            $('#btn-reset').on('click', function() {
                $('#filter-status').val('');
                $('#filter-kode-material').val('');
                $('#filter-tanggal-dari').val('');
                $('#filter-tanggal-sampai').val('');
                table.draw();
            });

            // Enter key event for text inputs
            $('#filter-kode-material').on('keypress', function(e) {
                if (e.which == 13) {
                    table.draw();
                }
            });

            // Auto filter on date change
            $('#filter-tanggal-dari, #filter-tanggal-sampai').on('change', function() {
                table.draw();
            });

            // Auto filter on status change
            $('#filter-status').on('change', function() {
                table.draw();
            });
        });
    </script>
@endpush
