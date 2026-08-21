@extends('layouts.app')

@section('title', 'Ekspedisi')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ekspedisi</h1>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Menampilkan Seluruh Ekspedisi
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ekspedisi-table" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>No Exp</th>
                            <th>Lot</th>
                            <th>Tgl Kirim</th>
                            <th>Tgl Kembali</th>
                            <th>Tujuan</th>
                            <th>Keterangan</th>
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
            $('#ekspedisi-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: 'lfrtip',
                ajax: function (data, callback, settings) {
                    $.get("{{ route('ekspedisi.data') }}", {
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
                        data: 'no_exp',
                        name: 'no_exp',
                        className: 'text-center'
                    },
                    {
                        data: 'lot',
                        name: 'lot',
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_kirim',
                        name: 'tgl_kirim',
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_kembali',
                        name: 'tgl_kembali',
                        className: 'text-center'
                    },
                    {
                        data: 'tujuan',
                        name: 'tujuan',
                        className: 'text-center'
                    },
                    {
                        data: 'ket_exp',
                        name: 'ket_exp'
                    }
                ]
            });
        });
    </script>
@endpush
