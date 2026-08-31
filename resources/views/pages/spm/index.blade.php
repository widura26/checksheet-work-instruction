@extends('layouts.app')

@section('title', 'Manajemen SPM')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }
        .dataTables_scrollBody {
            overflow-x: auto !important;
        }
        #table-spm {
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
                <h5 class="card-title mb-0">Manajemen SPM</h5>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" id="filter_no_spm" class="form-control form-control-sm" placeholder="No. SPM" style="width: 150px;">
                <select id="filter_project" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">Semua Project</option>
                </select>
                <input type="date" id="filter_tanggal_spm" class="form-control form-control-sm" style="width: 160px;">
                <select id="filter_status" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">Semua Status</option>
                </select>
                <button id="filter-button" class="btn btn-primary btn-sm">
                    <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"></path></svg> Filter
                </button>
                <button id="reset-filter-button" class="btn btn-secondary btn-sm">
                    <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"></path></svg> Reset
                </button>
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                    <a href="{{ route('spm.create') }}" class="btn btn-primary btn-sm">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg> Buat SPM
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table-spm" class="table table-bordered table-hover" style="width: 100%;">
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
