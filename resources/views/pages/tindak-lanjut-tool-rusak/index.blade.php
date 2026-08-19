@extends('layouts.app')

@section('title', 'Tindak Lanjut Tool Rusak')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            Filter
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kode_material">Kode Material</label>
                        <input type="text" id="kode_material" name="kode_material" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="no_spr">No SPR</label>
                        <input type="text" id="no_spr" name="no_spr" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="project_id">Project</label>
                        <select id="project_id" name="project_id" class="form-control">
                            <option value="">-- Semua Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->nama_project }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="lokasi_id">Lokasi</label>
                        <select id="lokasi_id" name="lokasi_id" class="form-control">
                            <option value="">-- Semua Lokasi --</option>
                            @foreach ($lokasi as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_posisi_ct }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="sudah_di_pr">Sudah di PR</option>
                            <option value="belum_di_pr">Belum di PR</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <select id="keterangan" name="keterangan" class="form-control">
                            <option value="">-- Semua Keterangan --</option>
                            <option value="sudah_diganti">Sudah Diganti</option>
                            <option value="belum_diganti">Belum Diganti</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button id="filter" class="btn btn-primary">Filter</button>
                    <button id="reset" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                @if (Auth::user()->hasRole('admin'))
                    <a href="{{ route('pr.create') }}" class="btn btn-success text-white">
                        Input PR
                    </a>
                @endif
            </div>

            <div class="table-responsive mt-3">
                <table id="table-tindak-lanjut" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>No SPR</th>
                            <th>Deskripsi</th>
                            <th>Deskripsi Kerusakan</th>
                            <th>Project</th>
                            <th>Lokasi</th>
                            <th>Status Perbaikan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Keterangan</th>
                            <th class="text-center">File SPR</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
    </style>
@endpush

@push('js')
    <script type="module">
        $(document).ready(function() {
            var table = $('#table-tindak-lanjut').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                ajax: {
                    url: "{{ route('tindak-lanjut-tool-rusak.data') }}",
                    data: function(d) {
                        d.kode_material = $('#kode_material').val();
                        d.no_spr = $('#no_spr').val();
                        d.project_id = $('#project_id').val();
                        d.lokasi_id = $('#lokasi_id').val();
                        d.status_perbaikan = $('#status_perbaikan').val();
                        d.status = $('#status').val();
                        d.keterangan = $('#keterangan').val();
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
                        data: 'no_spr',
                        name: 'no_spr'
                    },
                    {
                        data: 'deskripsi_tool',
                        name: 'deskripsi_tool'
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
                        data: 'status_perbaikan',
                        name: 'status_perbaikan',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'file_spr',
                        name: 'file_spr',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                autoWidth: false,
            });

            $('#filter').on('click', function() {
                table.ajax.reload();
            });

            $('#reset').on('click', function() {
                $('#kode_material').val('');
                $('#project_id').val('');
                $('#lokasi_id').val('');
                $('#status_perbaikan').val('tidak_bisa_diperbaiki').trigger('change');
                $('#status').val('');
                $('#keterangan').val('');
                $('#no_spr').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
