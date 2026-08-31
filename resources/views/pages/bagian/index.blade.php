@extends('layouts.app')

@section('title', 'Daftar Lokasi Bagian')

@push('css')
    <style>
        .lokasi-cell {
            padding: 0.5rem;
            font-weight: bold;
            border-radius: 0.25rem;
            color: white;
            text-align: center;
        }

        .lokasi-fabrikasi {
            background-color: #4CAF50;
        }

        .lokasi-finishing {
            background-color: #2196F3;
        }

        .lokasi-default {
            background-color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar lokasi Bagian</h5>
            <small class="text-body-secondary">Halaman ini menampilkan daftar lokasi bagian</small>
        </div>
        <div class="card-body">
            {{-- <a class="btn btn-primary mb-3" href="{{ route('bagian.create') }}">Input Bagian</a> --}}

            <div class="table-responsive">
                <table id="table-bagian" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Bagian</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
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
            var table = $('#table-bagian').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('bagian.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_bagian',
                        name: 'nama_bagian'
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi',
                        className: 'text-center',
                        render: function(data, type, row) {
                            let lokasiClass = 'lokasi-default';
                            if (data) {
                                if (data.toLowerCase().includes('fabrikasi')) {
                                    lokasiClass = 'lokasi-fabrikasi';
                                } else if (data.toLowerCase().includes('finishing')) {
                                    lokasiClass = 'lokasi-finishing';
                                }
                            }
                            return '<span class="lokasi-cell ' + lokasiClass + '">' + data +
                                '</span>';
                        }
                    },
                    {
                        data: 'id',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-warning btn-sm" href="/bagian/${data}/edit">
                                    <svg class="icon">
                                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-pencil') }}"></use>
                                    </svg>
                                </a>
                                <button class="btn btn-danger btn-sm deleteButton" data-id="${data}">
                                    <svg class="icon">
                                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-trash') }}"></use>
                                    </svg>
                                </button>
                            </div>`;
                        }
                    }
                ]
            });

            $(document).on('click', '.deleteButton', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/bagian/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Gagal!',
                                    'Terjadi kesalahan saat menghapus data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
