@extends('layouts.app')

@section('title', 'Manajemen SPM')

@section('content')

    <div class="card mb-3">
        <div class="card-header">
            Filter Data
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filter_no_spm">No. SPM</label>
                    <input type="text" id="filter_no_spm" class="form-control" placeholder="Filter No. SPM">
                </div>
                <div class="col-md-3">
                    <label for="filter_project">Project</label>
                    <select id="filter_project" class="form-select">
                        <option value="">Semua Project</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_tanggal_spm">Tanggal SPM</label>
                    <input type="date" id="filter_tanggal_spm" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="filter_status">Status</label>
                    <select id="filter_status" class="form-select">
                        <option value="">Semua Status</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button id="filter-button" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i>
                    Filter
                </button>
                <button id="reset-filter-button" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header">
            Menampilkan Seluruh Surat Permintaan Material (SPM)
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex align-items-center">
                </div>
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                    <a href="{{ route('spm.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>
                        Buat SPM
                    </a>
                @endif
            </div>

            <div class="table-responsive">
                <table id="table-spm" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>No. SPM</th>
                            <th>Project</th>
                            <th>Kepada</th>
                            <th>Tanggal SPM</th>
                            <th>Status</th>
                            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                                <th class="text-center">Action</th>
                            @endif
                            <th>Kedatangan</th>
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
        window.SPM = {
            routes: {
                data: "{{ route('spm.getData') }}",
                getProjects: "{{ route('spm.getProjects') }}",
                getStatuses: "{{ route('spm.getStatuses') }}",
                create: "{{ route('spm.create') }}",
                show: "{{ route('spm.show', '__ID__') }}",
                print: "{{ route('spm.print', '__ID__') }}",
                terima: "{{ route('spm.terima', '__ID__') }}",
                cancel: "{{ route('spm.cancel', '__ID__') }}",
                download: "{{ route('spm.download', '__ID__') }}",
                ajukanUlang: "{{ route('spm.ajukan-ulang', '__ID__') }}",
                destroy: "{{ route('spm.destroy', '__ID__') }}"
            },
            isAdmin: {{ Auth::user()->hasRole('admin') ? 'true' : 'false' }}
        };
    </script>
    <script type="module">
        $(function() {
            $.get(SPM.routes.getProjects, function(data) {
                var $filter = $('#filter_project');
                $filter.empty().append('<option value="">Semua Project</option>');
                data.forEach(function(p) { $filter.append(new Option(p.name, p.project_id)); });
            });

            $.get(SPM.routes.getStatuses, function(data) {
                var $filter = $('#filter_status');
                $filter.empty().append('<option value="">Semua Status</option>');
                data.forEach(function(s) { $filter.append(new Option(s.name, s.value)); });
            });

            var table = $('#table-spm').DataTable({
                processing: false,
                serverSide: true,
                ajax: function(data, callback, settings) {
                    $.get(SPM.routes.data, {
                        page: (data.start / data.length) + 1,
                        per_page: data.length,
                        search: data.search.value,
                        no_spm: $('#filter_no_spm').val(),
                        project_id: $('#filter_project').val(),
                        tanggal_spm: $('#filter_tanggal_spm').val(),
                        status: $('#filter_status').val()
                    }).done(function(json) {
                        callback({
                            draw: data.draw,
                            recordsTotal: json.total,
                            recordsFiltered: json.total,
                            data: json.data
                        });
                    });
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) { return meta.row + 1; }
                    },
                    { data: 'no_spm', name: 'no_spm' },
                    { data: 'project_name', name: 'project_name', defaultContent: '-' },
                    { data: 'kepada', name: 'kepada', defaultContent: '-' },
                    {
                        data: 'tanggal_spm',
                        name: 'tanggal_spm',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(data) {
                            var map = {
                                'COMPLETED': '<span class="badge bg-success">SELESAI</span>',
                                'WAITING': '<span class="badge bg-secondary">MENUNGGU</span>',
                                'CANCELED': '<span class="badge bg-danger">DI BATALKAN</span>'
                            };
                            return map[data] || '<span class="badge bg-light text-dark">Unknown</span>';
                        }
                    },
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (row.status === 'CANCELED') {
                                return '<a href="' + SPM.routes.ajukanUlang.replace('__ID__', row.id) + '" class="btn btn-info text-white rounded btn-sm">Ajukan Ulang</a>';
                            }
                            var iconUrl = '{{ asset("template/vendors/@coreui/icons/svg/free.svg") }}';
                            var btns = '<div class="btn-group gap-2">';
                            btns += '<a href="' + SPM.routes.show.replace('__ID__', row.id) + '" class="btn btn-info text-white rounded btn-sm" title="Lihat">';
                            btns += '<svg class="icon"><use xlink:href="' + iconUrl + '#cil-fullscreen"></use></svg></a>';
                            btns += '<a href="' + SPM.routes.print.replace('__ID__', row.id) + '" target="_blank" class="btn btn-warning text-white rounded btn-sm" title="Cetak">';
                            btns += '<svg class="icon"><use xlink:href="' + iconUrl + '#cil-print"></use></svg></a>';
                            if (SPM.isAdmin && row.status === 'WAITING' && !row.file_ttd) {
                                btns += '<form action="' + SPM.routes.cancel.replace('__ID__', row.id) + '" method="POST" onsubmit="return confirm(\'Yakin ingin membatalkan?\')" style="display:inline;">';
                                btns += '@csrf @method("POST")';
                                btns += '<button type="submit" class="btn btn-danger text-white rounded btn-sm" title="Batalkan">';
                                btns += '<svg class="icon"><use xlink:href="' + iconUrl + '#cil-x-circle"></use></svg></button></form>';
                            }
                            btns += '</div>';
                            return btns;
                        }
                    },
                    @endif
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var iconUrl = '{{ asset("template/vendors/@coreui/icons/svg/free.svg") }}';
                            if (row.status !== 'COMPLETED' && row.status !== 'PARTIAL' && row.status !== 'CANCELED') {
                                return '<a href="' + SPM.routes.terima.replace('__ID__', row.id) + '" class="btn btn-success btn-sm text-white">Terima Barang</a>';
                            }
                            if (row.file_ttd) {
                                return '<a href="' + SPM.routes.download.replace('__ID__', row.id) + '" class="btn btn-success btn-sm text-white" target="_blank"><svg class="icon"><use xlink:href="' + iconUrl + '#cil-cloud-download"></use></svg></a>';
                            }
                            return '';
                        }
                    }
                ]
            });

            $('#filter-button').click(function() { table.ajax.reload(); });
            $('#reset-filter-button').click(function() {
                $('#filter_no_spm').val('');
                $('#filter_project').val('');
                $('#filter_tanggal_spm').val('');
                $('#filter_status').val('');
                table.ajax.reload();
            });

            @if (session('success'))
                Swal.fire({ title: 'Berhasil!', text: '{{ session('success') }}', icon: 'success' });
            @endif
            @if (session('error'))
                Swal.fire({ title: 'Gagal!', text: '{{ session('error') }}', icon: 'error' });
            @endif
        });
    </script>
@endpush
