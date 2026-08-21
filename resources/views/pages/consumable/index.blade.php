@extends('layouts.app')

@section('title', 'Daftar Consumable')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">List Consumable</h5>
            <small class="text-body-secondary">Halaman ini menampilkan data material consumable pada central tools</small>
        </div>
        <div class="card-body">
            <div class="row mb-3">
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
            <div class="table-responsive">
                <div class="w-100 d-flex justify-content-end">
                    <button type="button" id="btn-export-consumable" class="btn btn-outline-primary">
                        <i class="fas fa-file-export me-1"></i>
                        Export to Excel
                    </button>
                </div>
                <table id="table-consumable" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            <th>Satuan</th>
                            <th>Posisi CT</th>
                            <th>Project</th>
                            <th>Stock</th>
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
            var table = $('#table-consumable').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ route('consumable.index') }}",
                    data: function(d) {
                        d.kode_material = $('#kode_material_filter').val();
                        d.deskripsi = $('#deskripsi_filter').val();
                        d.spesifikasi = $('#spesifikasi_filter').val();
                        d.posisi_ct_id = $('#posisi_ct_filter').val();
                        d.project_id = $('#project_filter').val();
                        d.vendor = $('#vendor_filter').val();
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
                        name: 'tool.satuan'
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
                        name: 'stok',
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
                scrollX: true, // Enable horizontal scroll untuk semua kolom
                scrollCollapse: true,
                drawCallback: function(settings) {
                    var info = this.api().page.info();
                    var empty = info.recordsDisplay === 0;
                    $('#btn-export-consumable').prop('disabled', empty).css('cursor', empty ? 'not-allowed' : '');
                },
            });
            $('#filterBtn').click(function() {
                table.draw();
            });

            $('#btn-export-consumable').click(function() {
                var url = "{{ route('consumable.export') }}";
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
    </script>
@endpush
