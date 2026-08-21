@extends('layouts.app')

@section('title', 'Work Instruction')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Data Work Instruction</h5>
            <small class="text-body-secondary">Daftar data Work Instruction (wi_trns)</small>
        </div>
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

            <div class="table-responsive">
                <table id="table-wi" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>No Part</th>
                            <th>Nama</th>
                            <th>Mat</th>
                            <th>T</th>
                            <th>H</th>
                            <th>B</th>
                            <th>L</th>
                            <th>Ws1</th>
                            <th>Ws2</th>
                            <th>Ws3</th>
                            <th>Ws4</th>
                            <th>Ws5</th>
                            <th>Shop 1</th>
                            <th>Shop 2</th>
                            <th>Des</th>
                            <th>No Cp</th>
                            <th>Tgl Cp</th>
                            <th>Qty/ lot</th>
                            <th>No Bpm</th>
                            <th>Tgl Bpm</th>
                            <th>Tgl Wi Trn</th>
                            <th>Keterangan</th>
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
            var table = $('#table-wi').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                info: false,
                ajax: {
                    url: "{{ route('wi.data') }}",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'part_no',
                        name: 'part_no',
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false
                    },
                    {
                        data: 'mat',
                        name: 'mat',
                        orderable: false
                    },
                    {
                        data: 't1',
                        name: 't1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'h1',
                        name: 'h1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'b1',
                        name: 'b1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'l1',
                        name: 'l1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws1',
                        name: 'ws1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws2',
                        name: 'ws2',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws3',
                        name: 'ws3',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws4',
                        name: 'ws4',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws5',
                        name: 'ws5',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'shop1',
                        name: 'shop1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'shop2',
                        name: 'shop2',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'des',
                        name: 'des',
                        orderable: false
                    },
                    {
                        data: 'no_ctplan',
                        name: 'no_ctplan',
                        orderable: false
                    },
                    {
                        data: 'tgl_ctplan',
                        name: 'tgl_ctplan',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'qty_lot',
                        name: 'qty_lot',
                        orderable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'no_bpm',
                        name: 'no_bpm',
                        orderable: false
                    },
                    {
                        data: 'tgl_bpm',
                        name: 'tgl_bpm',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_trn',
                        name: 'tgl_trn',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false
                    },
                ],
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
            });
        });
    </script>
@endpush
