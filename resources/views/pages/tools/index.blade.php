@extends('layouts.app')

@section('title', 'Manajemen Tool')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Menampilkan Seluruh Tool Yang Ada Di Central Tools
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex align-items-center">
                </div>
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                    {{-- <button onclick="ExportToExcel('xlsx')" class="btn btn-outline-primary">
                        <i class="fas fa-download me-1"></i>
                        Ekspor Data
                    </button> --}}
                @endif
            </div>

            <div class="row mb-3 d-flex align-items-center">
                <div class="col-md-4">
                    <input type="text" id="kode_material_filter" class="form-control" placeholder="Filter by Kode Material">
                </div>
                <div class="col-md-4">
                    <input type="text" id="deskripsi_filter" class="form-control" placeholder="Filter by Deskripsi">
                </div>
                <div class="col-md-4">
                    <input type="text" id="spesifikasi_filter" class="form-control" placeholder="Filter by Spesifikasi">
                </div>
                <div class="col-md-4 mt-3">
                    <select id="posisi_ct_filter" class="form-control">
                        <option value="">-- Filter by Posisi CT --</option>
                        @foreach ($posisiCTs as $posisi)
                            <option value="{{ $posisi->id }}">{{ $posisi->nama_posisi_ct }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mt-3">
                    <select id="project_filter" class="form-control">
                        <option value="">-- Filter by Project --</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <button id="filterBtn" class="btn btn-primary">Filter</button>
                    <button id="resetBtn" class="btn btn-secondary">Reset</button>
                </div>
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

            <div class="row mb-3">
                @if (Auth::user()->hasRole('admin'))
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <livewire:material.material-import />
                        <button type="button" id="btn-export-tool" class="btn btn-outline-primary">
                            <i class="fas fa-file-export me-1"></i>
                            Export to Excel
                        </button>
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <table id="table-tool" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            <th>Satuan</th>
                            <th>Posisi CT</th>
                            <th>Project</th>
                            <th>Stok</th>
                            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                                <th class="text-center">Action</th>
                            @endif
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
            var table = $('#table-tool').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('tool-get-data.data') }}",
                    data: function(d) {
                        d.kode_material = $('#kode_material_filter').val();
                        d.deskripsi = $('#deskripsi_filter').val();
                        d.spesifikasi = $('#spesifikasi_filter').val();
                        d.posisi_ct_id = $('#posisi_ct_filter').val();
                        d.project_id = $('#project_filter').val();
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
                        name: 'tool.kode_material',
                        orderable: false,
                    },
                    {
                        data: 'deskripsi',
                        name: 'tool.deskripsi',
                        orderable: false,
                    },
                    {
                        data: 'spesifikasi',
                        name: 'tool.spesifikasi',
                        orderable: false,
                    },
                    {
                        data: 'satuan',
                        name: 'tool.satuan',
                        orderable: false,
                    },
                    {
                        data: 'nama_posisi_ct',
                        name: 'nama_posisi_ct'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'stok',
                        name: 'qty',
                        className: 'text-center'
                    },
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',

                        }
                    @endif
                ],
                autoWidth: false,
                drawCallback: function(settings) {
                    var info = this.api().page.info();
                    var empty = info.recordsDisplay === 0;
                    $('#btn-export-tool').prop('disabled', empty).css('cursor', empty ? 'not-allowed' : '');
                },
            });

            $('#filterBtn').click(function() {
                table.draw();
            });

            $('#btn-export-tool').click(function() {
                var url = "{{ route('tool.export') }}";
                var params = [];

                var kodeMaterial = $('#kode_material_filter').val();
                var deskripsi = $('#deskripsi_filter').val();
                var spesifikasi = $('#spesifikasi_filter').val();
                var posisiCtId = $('#posisi_ct_filter').val();
                var projectId = $('#project_filter').val();

                if (kodeMaterial) params.push('kode_material=' + encodeURIComponent(kodeMaterial));
                if (deskripsi) params.push('deskripsi=' + encodeURIComponent(deskripsi));
                if (spesifikasi) params.push('spesifikasi=' + encodeURIComponent(spesifikasi));
                if (posisiCtId) params.push('posisi_ct_id=' + encodeURIComponent(posisiCtId));
                if (projectId) params.push('project_id=' + encodeURIComponent(projectId));

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                window.location.href = url;
            });

            $('#resetBtn').click(function() {
                $('#kode_material_filter').val('');
                $('#deskripsi_filter').val('');
                $('#spesifikasi_filter').val('');
                $('#posisi_ct_filter').val('');
                $('#project_filter').val('');
                $('#vendor_filter').val('');
                table.draw();
            });
        });

        function ExportToExcel(type, dl) {
            var elt = document.getElementById('table-tool');
            var wb = XLSX.utils.table_to_book(elt, {
                sheet: "sheet1",
                autoSize: true
            });
            var currentDate = new Date();
            var dateString = currentDate.toISOString().slice(0, 10);
            var fileName = 'Daftar Tools Central Tools ' + dateString + '.' + (type || 'xlsx');
            return dl ?
                XLSX.write(wb, {
                    bookType: type,
                    bookSST: true,
                    type: 'base64'
                }) :
                XLSX.writeFile(wb, fileName);
        }
    </script>
@endpush
