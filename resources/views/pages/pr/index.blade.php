@extends('layouts.app')

@section('title', 'Daftar Purchase Requisition')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Purchase Requisition</h5>
            @if (Auth::user()->hasRole(['admin']))
                <a href="{{ route('pr.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Input PR Baru
                </a>
            @endif
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter-status" class="form-label">Status PR</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum_datang">Belum Datang</option>
                        <option value="sudah_datang">Sudah Datang</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-tanggal-dari" class="form-label">Tanggal PR Dari</label>
                    <input type="date" id="filter-tanggal-dari" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="filter-tanggal-sampai" class="form-label">Tanggal PR Sampai</label>
                    <input type="date" id="filter-tanggal-sampai" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="filter-no-pr" class="form-label">No. PR</label>
                    <input type="text" id="filter-no-pr" class="form-control" placeholder="Cari No. PR...">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <button type="button" id="btn-filter" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="button" id="btn-reset" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="pr-table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No. PR</th>
                            <th>Tanggal PR</th>
                            <th>Status</th>
                            <th>Jumlah Tools</th>
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

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus Purchase Requisition <strong id="pr-name"></strong>?</p>
                    <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .row {
            margin-bottom: 0.5rem;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
        }
    </style>
@endpush

@push('js')
    <script type="module">
        $(document).ready(function() {
            var table = $('#pr-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                ajax: {
                    url: '{{ route('pr.datatable') }}',
                    data: function(d) {
                        d.status_filter = $('#filter-status').val();
                        d.tanggal_dari_filter = $('#filter-tanggal-dari').val();
                        d.tanggal_sampai_filter = $('#filter-tanggal-sampai').val();
                        d.no_pr_filter = $('#filter-no-pr').val();
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
                        data: 'no_pr',
                        name: 'no_pr',
                        render: function(data, type, row) {
                            return '<strong>' + data + '</strong>';
                        }
                    },
                    {
                        data: 'tanggal_pr',
                        name: 'tanggal_pr',
                        className: 'text-center'
                    },
                    {
                        data: 'status_pr',
                        name: 'status_pr',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'details_count',
                        name: 'details_count',
                        className: 'text-center',
                    },
                    @if (Auth::user()->hasRole(['admin']))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    @endif
                ],
                order: [
                    [5, 'desc']
                ], // Order by created_at desc
                autoWidth: false,
            });

            // Filter button click event
            $('#btn-filter').on('click', function() {
                table.draw();
            });

            // Reset button click event
            $('#btn-reset').on('click', function() {
                $('#filter-status').val('');
                $('#filter-tanggal-dari').val('');
                $('#filter-tanggal-sampai').val('');
                $('#filter-no-pr').val('');
                table.draw();
            });

            // Enter key event for text inputs
            $('#filter-no-pr').on('keypress', function(e) {
                if (e.which == 13) {
                    table.draw();
                }
            });

            // Auto filter on date change
            $('#filter-tanggal-dari, #filter-tanggal-sampai').on('change', function() {
                table.draw();
            });

            // Auto filter on status change
            $('#filter-status').on('change', function() {
                table.draw();
            });

            // Auto hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            // Handle Sudah Datang button click with SweetAlert
            $(document).on("click", ".sudah-datang-btn", function() {
                const prId = $(this).data("id");
                const noPr = $(this).data("no-pr");

                Swal.fire({
                    title: "Konfirmasi",
                    text: `Apakah Anda yakin PR dengan nomor ${noPr} sudah datang?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Batal",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('pr.sudah-datang', '') }}/" + prId;
                    }
                });
            });
            $(document).on("click", ".sudah-di-ct-btn", function() {
                const prId = $(this).data("id");
                const noPr = $(this).data("no-pr");

                Swal.fire({
                    title: "Konfirmasi",
                    text: `Apakah Anda yakin PR dengan nomor ${noPr} sudah di CT?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Batal",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request
                        $.ajax({
                            url: "{{ route('pr.sudah-datang-ct', '') }}/" + prId,
                            type: "GET",
                            success: function(response) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Status PR berhasil diupdate menjadi Sudah di CT",
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                // Reload datatable
                                $("#pr-table").DataTable().ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Terjadi kesalahan saat mengupdate status PR",
                                    icon: "error",
                                });
                            },
                        });
                    }
                });
            });
        });
    </script>
@endpush
