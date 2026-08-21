@extends('layouts.app')

@section('title', 'Cutting Plan')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cutting Plan</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="cp-table" class="table table-striped table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>ID CP</th>
                            <th>No CP</th>
                            <th>Material</th>
                            <th>Spec Material</th>
                            <th>Qty</th>
                            <th>Shop</th>
                            <th>Grup</th>
                            <th>Lot</th>
                            <th>Qty Lot</th>
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
            $('#cp-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: 'lfrtip',
                ajax: function (data, callback, settings) {
                    $.get("{{ route('cp.data') }}", {
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
                        data: 'id_cp',
                        name: 'id_cp',
                        className: 'text-center'
                    },
                    {
                        data: 'no_cp',
                        name: 'no_cp',
                        className: 'text-center'
                    },
                    {
                        data: 'mat',
                        name: 'mat'
                    },
                    {
                        data: 'spec_mat',
                        name: 'spec_mat'
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        className: 'text-center'
                    },
                    {
                        data: 'shop',
                        name: 'shop',
                        className: 'text-center'
                    },
                    {
                        data: 'grup',
                        name: 'grup',
                        className: 'text-center'
                    },
                    {
                        data: 'lot',
                        name: 'lot',
                        className: 'text-center'
                    },
                    {
                        data: 'qty_lot',
                        name: 'qty_lot',
                        className: 'text-center'
                    }
                ]
            });
        });
    </script>
@endpush
