@extends('layouts.app')

@section('title', 'List Dokumen Pengambilan')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            Menampilkan Seluruh Dokumen Pengambilan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table-dokumen-pengambilan" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nomor Dokumen</th>
                            <th>Project</th>
                            <th>Dokumen Pengambilan</th>
                            <th>File Dokumen</th>
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
            var table = $('#table-dokumen-pengambilan').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('dokumen-pengambilan.getData') }}",
                },
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tanggal_update',
                        name: 'tanggal_update',
                        className: 'text-center'
                    },
                    {
                        data: 'nomor_dokumen',
                        name: 'nomor_dokumen'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'dokumen_pengambilan',
                        name: 'dokumen_pengambilan',
                        className: 'text-center'
                    },

                    {
                        data: 'file_dokumen',
                        name: 'file_dokumen',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                autoWidth: false,
            });

            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success'
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    icon: 'error'
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    title: 'Gagal!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    icon: 'error'
                });
            @endif
        });
    </script>
@endpush
