@extends('layouts.app')

@section('title', 'Detail')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Detail Project: {{ $project->nama_project }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Project ID</label>
                        <input type="text" class="form-control" value="{{ $project->project_id }}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Project</label>
                        <input type="text" class="form-control" value="{{ $project->nama_project }}" disabled>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Daftar Material Di Project {{ $project->nama_project }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tools-table">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            <th>Posisi</th>
                            <th>Jumlah Stok</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(function() {
            $('#tools-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: 'lfrtip',
                ajax: '{{ route('projects.tools.data', $project) }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'kode_material',
                        name: 'tool.kode_material'
                    },
                    {
                        data: 'deskripsi',
                        name: 'tool.deskripsi'
                    },
                    {
                        data: 'spesifikasi',
                        name: 'tool.spesifikasi'
                    },
                    {
                        data: 'posisi',
                        name: 'tool.posisiCT.nama_posisi_ct'
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        className: 'text-center'
                    }
                ]
            });
        });
    </script>
@endpush
