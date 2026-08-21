@extends('layouts.app')

@section('title', 'Manufacture Drawing')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-responsive">
                <table id="md-table" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>ID MDR</th>
                            <th>Project ID</th>
                            <th>OKA ID</th>
                            <th>Tanggal MDR</th>
                            <th>Masalah</th>
                            <th>Lanjut</th>
                            <th>Input</th>
                            <th>Dampak</th>
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
            $('#md-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: 'lfrtip',
                ajax: function (data, callback, settings) {
                    $.get("{{ route('md.data') }}", {
                        page: (data.start / data.length) + 1,
                        per_page: data.length,
                        search: data.search.value
                    }).done(function (json) {
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
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'id_mdr',
                        name: 'id_mdr'
                    },
                    {
                        data: 'project_id',
                        name: 'project_id'
                    },
                    {
                        data: 'oka_id',
                        name: 'oka_id'
                    },
                    {
                        data: 'tgl_mdr',
                        name: 'tgl_mdr'
                    },
                    {
                        data: 'masalah',
                        name: 'masalah'
                    },
                    {
                        data: 'lanjut',
                        name: 'lanjut'
                    },
                    {
                        data: 'input',
                        name: 'input'
                    },
                    {
                        data: 'dampak',
                        name: 'dampak'
                    }
                ]
            });
        });
    </script>
@endpush
