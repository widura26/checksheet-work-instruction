@extends('layouts.app')

@section('title', 'Surat Tanda Terima Penyerahan Produk')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Surat Tanda Terima Penyerahan Produk</h5>
            <small class="text-body-secondary">Daftar data STTP (fabrication)</small>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" id="search_filter" class="form-control"
                        placeholder="Cari No STTP / No Part / Nama / Proyek / Oka">
                </div>
                <div class="col-md-6">
                    <button id="filter-button" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>
                        Cari
                    </button>
                    <button id="reset-filter-button" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i>
                        Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="table-sttp" class="table table-bordered table-hover" style="width:100%">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>No Sttp</th>
                            <th>No Part</th>
                            <th>Nama</th>
                            <th>Rev</th>
                            <th>Oka</th>
                            <th>Proyek</th>
                            <th>Lot</th>
                            <th>Qty lot</th>
                            <th>Qty Krm</th>
                            <th>Tgl Kirim</th>
                            <th>Dari</th>
                            <th>Ke</th>
                            <th>Pengirim</th>
                            <th>Penerima</th>
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
            var table = $('#table-sttp').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('sttp.data') }}",
                    data: function(d) {
                        d.search = $('#search_filter').val();
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
                        data: 'nosttpp',
                        name: 'nosttpp',
                        orderable: false
                    },
                    {
                        data: 'no_part',
                        name: 'no_part',
                        orderable: false
                    },
                    {
                        data: 'name_part',
                        name: 'name_part',
                        orderable: false
                    },
                    {
                        data: 'rev',
                        name: 'rev',
                        orderable: false
                    },
                    {
                        data: 'oka',
                        name: 'oka',
                        orderable: false
                    },
                    {
                        data: 'proyek',
                        name: 'proyek',
                        orderable: false
                    },
                    {
                        data: 'lot',
                        name: 'lot',
                        orderable: false
                    },
                    {
                        data: 'qty_lot',
                        name: 'qty_lot',
                        orderable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'qty_kirim',
                        name: 'qty_kirim',
                        orderable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'tgl_kirim',
                        name: 'tgl_kirim',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'dari',
                        name: 'dari',
                        orderable: false
                    },
                    {
                        data: 'ke',
                        name: 'ke',
                        orderable: false
                    },
                    {
                        data: 'pengirim',
                        name: 'pengirim',
                        orderable: false
                    },
                    {
                        data: 'penerima',
                        name: 'penerima',
                        orderable: false
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

            $('#filter-button').click(function() {
                table.ajax.reload();
            });

            $('#reset-filter-button').click(function() {
                $('#search_filter').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
