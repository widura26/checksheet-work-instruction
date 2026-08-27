@extends('layouts.app')

@section('title', 'Roles')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }

        #table-roles {
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
                <h5 class="card-title mb-0">Data Role</h5>
            </div>
            @if (Auth::user()->hasRole(['admin']))
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg>
                        Tambah Role
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search_input"
                            placeholder="Cari Nama Role...">
                        <button class="btn btn-primary" type="button" id="btnSearch">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                        <button class="btn btn-secondary" type="button" id="btnReset">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="table-roles" class="table table-bordered table-hover" style="width: 100%;">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Role</th>
                            <th class="text-center">Action</th>
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
        $(function() {
            let tableRoles = $('#table-roles').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                ajax: function(data, callback, settings) {
                    $.get("{{ route('roles.data') }}", {
                        page: (data.start / data.length) + 1,
                        per_page: data.length,
                        search: data.search.value
                    }).done(function(json) {
                        callback({
                            draw: data.draw,
                            recordsTotal: json.total,
                            recordsFiltered: json.total,
                            data: json.data
                        });
                    });
                },
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ]
            });

            $('#btnSearch').on('click', function() {
                tableRoles.search($('#search_input').val()).draw();
            });
            $('#search_input').on('keypress', function(e) {
                if (e.which === 13) {
                    tableRoles.search(this.value).draw();
                }
            });
            $('#btnReset').on('click', function() {
                $('#search_input').val('');
                tableRoles.search('').ajax.reload();
            });
        });
    </script>
@endpush
