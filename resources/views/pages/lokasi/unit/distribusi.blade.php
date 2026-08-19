@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="m-0">Distribusi Material - Unit {{ $unit->nama_unit }}</h5>
                        <small class="text-muted">
                            Bagian: {{ $unit->bagian->nama_bagian }} | 
                            Departemen: {{ $unit->bagian->departemen->nama_departemen }}
                        </small>
                    </div>
                    <a href="{{ route('lokasi.unit.index') }}" class="btn btn-secondary">
                        <svg class="icon me-2" style="width: 16px; height: 16px;">
                            <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-arrow-left') }}"></use>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="distribusi-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Peminjam/Penerima</th>
                                <th>Bagian</th>
                                <th>Jumlah Material</th>
                                <th>No. BAST</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(function() {
            $('#distribusi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lokasi.unit.distribusi.data', $unit->id) }}',
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal_pinjam_diserahkan'
                    },
                    {
                        data: 'type_badge',
                        name: 'type'
                    },
                    {
                        data: 'kategori_badge',
                        name: 'kategori'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'nama_peminjam',
                        name: 'nama_peminjam'
                    },
                    {
                        data: 'bagian.nama_bagian',
                        name: 'bagian.nama_bagian'
                    },
                    {
                        data: 'jumlah_material',
                        name: 'jumlah_material',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_bast',
                        name: 'no_bast'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[1, 'desc']]
            });
        });
    </script>
@endpush
