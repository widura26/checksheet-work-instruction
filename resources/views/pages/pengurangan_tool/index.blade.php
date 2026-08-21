@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="card-title mb-0">Riwayat Pengurangan Stok Tool</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pengurangan-tool-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Kode Material</th>
                            <th>Deskripsi Tool</th>
                            <th>Jumlah Dikurangi</th>
                            <th>Alasan</th>
                            <th>Project</th>
                            <th>Lokasi CT</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data will be populated by DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            $('#pengurangan-tool-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pengurangan-tool.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_pengurangan',
                        name: 'tanggal_pengurangan'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'tool.kode_material',
                        name: 'tool.kode_material'
                    },
                    {
                        data: 'tool.deskripsi',
                        name: 'tool.deskripsi'
                    },
                    {
                        data: 'qty_dikurangi',
                        name: 'qty_dikurangi',
                    },
                    {
                        data: 'alasan',
                        name: 'alasan',
                    },
                    {
                        data: 'project',
                        name: 'project',
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
