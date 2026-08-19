@extends('layouts.app')
@section('title', 'Daftar Consumable')
@section('content')

    @if (session('success'))
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card Container -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">List Consumable</h5>
                <small class="text-muted">Halaman ini menampilkan data consumable tools pada central tools</small>
            </div>
            <div class="d-flex">
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                    <a class="btn btn-outline-success form-control ms-2" href="{{ route('consumable.create') }}">Input
                        Consumable Tool</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row" style="margin-bottom:15px">
                <div class="col-md-6 col-lg-12 ml-auto text-md-right mt-2 mt-md-0">
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                        <button onclick="ExportToExcel('xlsx')" class="btn btn-info">
                            <svg class="icon">
                                <use
                                    xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-data-transfer-down') }}">
                                </use>
                            </svg> Ekspor
                        </button>
                    @endif
                </div>
            </div>

            <div class="col">
                <div class="table-responsive" style="max-height:750px; overflow-y:auto; padding-bottom:50px;">
                    <table id="table-consumable" class="display">
                        <thead class="text-center sticky-header">
                            <tr>
                                <th>No</th>
                                <th>Kode Material</th>
                                <th>Deskripsi</th>
                                <th>Spesifikasi</th>
                                <th>Satuan</th>
                                <th>Posisi CT</th>
                                <th>Project</th>
                                <th>Stock</th>
                                <th>Vendor</th>
                                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')

    <script type="module">
        $(document).ready(function() {
            var table = $('#table-consumable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('consumable.data') }}", // Route to get data for consumables
                },
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 0
                    },
                    {
                        responsivePriority: 2,
                        targets: 1
                    },
                    {
                        responsivePriority: 3,
                        targets: 2
                    },
                    {
                        responsivePriority: 4,
                        targets: 3
                    },
                    {
                        responsivePriority: 10,
                        targets: -1
                    }
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'kode_material',
                        name: 'kode_material'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'spesifikasi',
                        name: 'spesifikasi'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        className: 'text-center'
                    },
                    {
                        data: 'posisi_c_t.nama_posisi_ct',
                        name: 'posisi_c_t.nama_posisi_ct'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'stok',
                        name: 'stok',
                        className: 'text-center'
                    },
                    {
                        data: 'vendor',
                        name: 'vendor'
                    },
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-primary btn-sm" href="/consumable/${data}/edit">
                                            <svg class="icon">
                                                <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-pencil') }}"></use>
                                            </svg> Edit
                                        </a>
                                        <button class="btn btn-danger btn-sm deleteButton" data-id="${data}">
                                            <svg class="icon">
                                                <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-trash') }}"></use>
                                            </svg> Hapus
                                        </button>
                                    </div>`;
                            }
                        }
                    @endif
                ],
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
            });

            // Event delegation for delete button
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
                        $.ajax({
                            url: `/consumable/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
    <script type="module">
        function ExportToExcel(type, dl) {
            var elt = document.getElementById('table-consumable');
            var wb = XLSX.utils.table_to_book(elt, {
                sheet: "sheet1",
                autoSize: true
            });

            var currentDate = new Date();
            var dateString = currentDate.toISOString().slice(0, 10);

            var fileName = 'Daftar Consumable Tools Central Tools ' + dateString + '.' + (type || 'xlsx');

            return dl ?
                XLSX.write(wb, {
                    bookType: type,
                    bookSST: true,
                    type: 'base64'
                }) :
                XLSX.writeFile(wb, fileName);
        }
    </script>
@endpush
