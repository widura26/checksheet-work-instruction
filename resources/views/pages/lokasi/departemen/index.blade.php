@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Departemen</h5>
                    @if (Auth::user()->hasRole('admin'))
                        <a href="{{ route('lokasi.departemen.create') }}" class="btn btn-primary">Tambah Departemen</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="departemen-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Departemen</th>
                            @if (Auth::user()->hasRole('admin'))
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script type="module">
        $(function() {
            $('#departemen-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                ajax: function(data, callback, settings) {
                    $.get("{{ route('lokasi.departemen.data') }}", {
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
                        data: 'nama_departemen',
                        name: 'nama_departemen'
                    },
                    @if (Auth::user()->hasRole('admin'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ]
            });
        });
    </script>
@endpush
