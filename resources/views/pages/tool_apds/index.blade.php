@extends('layouts.app')

@push('css')
    <style>
        .filter-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            margin-bottom: 1.5rem;
        }

        /* Dropdown button */
        .dropdown-toggle-btn {
            padding: 10px 18px;
            background-color: #ffffff;
            border: 2px solid #d1d5db;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .dropdown-toggle-btn:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }

        /* Dropdown menu */
        .dropdown-menu-modern {
            display: none;
            position: absolute;
            margin-top: 8px;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            padding: 10px;
            z-index: 1000;
        }

        .dropdown-item-modern {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0;
        }

        .dropdown-item-modern input[type="checkbox"] {
            accent-color: #10b981;
        }

        /* Modern buttons */
        .btn-modern {
            padding: 10px 16px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-success-modern {
            background-color: #10b981;
            color: white;
        }

        .btn-success-modern:hover {
            background-color: #059669;
        }

        .btn-info-modern {
            background-color: #3b82f6;
            color: white;
        }

        .btn-info-modern:hover {
            background-color: #2563eb;
        }

        /* Positioning dropdown */
        .dropdown-container {
            position: relative;
        }

        mark.green-highlight {
            background-color: #d4edda;
            /* Hijau muda (background success) */
            color: #155724;
            /* Hijau gelap (teks success) */
            font-weight: bold;
            padding: 0 4px;
            border-radius: 4px;
        }

        .preview-image {
            width: 100px;
            height: auto;
            cursor: pointer;
            transition: transform 0.2s;
            border-radius: 4px;
        }

        .preview-image:hover {
            transform: scale(1.05);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .table-container {
            max-height: 750px;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #e3e6f0;
            border-radius: 0.375rem;
            background-color: white;
        }

        /* Sticky Header yang Benar */
        .sticky-header {
            position: sticky;
            top: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            color: #5a5c69;
            padding: 12px 8px;
            white-space: nowrap;
        }
    </style>

@endpush

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
                    <h5 class="card-title mb-0">List APD Tools</h5>
                    <small class="text-muted">Halaman ini menampilkan data APD tools pada central tools</small>
                </div>
                <div class="d-flex">
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                        <a class="btn btn-outline-success form-control ms-2" href="{{ route('apd.create') }}">Input APD Tool</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row" style="margin-bottom:15px">
                    <div class="col-md-6 col-lg-12 ml-auto text-md-right mt-2 mt-md-0">
                        @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('staff'))
                            <button onclick="ExportToExcel('xlsx')" class="btn btn-info">
                                <svg class="icon">
                                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-data-transfer-down') }}"></use>
                                </svg> Ekspor
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col">
                    <div class="table-responsive" style="max-height:750px; overflow-y:auto; padding-bottom:50px;">
                        <table id="table-apd" class="table table-bordered table-hover">
                            <thead class="text-center sticky-header">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Material</th>
                                    <th>Deskripsi</th>
                                    <th>Spesifikasi</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Posisi CT</th>
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
            var table = $('#table-apd').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('apd.data') }}", // Route to get data for APD tools
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 2 },
                    { responsivePriority: 4, targets: 3 },
                    { responsivePriority: 10, targets: -1 }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'kode_material', name: 'kode_material' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'spesifikasi', name: 'spesifikasi' },
                    { data: 'qty', name: 'jumlah', className: 'text-center' },
                    { data: 'satuan', name: 'satuan' },
                    { data: 'nama_posisi_ct', name: 'nama_posisi_ct' },
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
                                        <a class="btn btn-primary btn-sm" href="/apd/${data}/edit">
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
                            url: `/apd/${id}`,
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
            var elt = document.getElementById('table-apd');
            var wb = XLSX.utils.table_to_book(elt, {
                sheet: "sheet1",
                autoSize: true
            });

            var currentDate = new Date();
            var dateString = currentDate.toISOString().slice(0, 10);

            var fileName = 'Daftar APD Tools Central Tools ' + dateString + '.' + (type || 'xlsx');

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
