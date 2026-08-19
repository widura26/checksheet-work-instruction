@extends('layouts.app')

@section('title', 'Daftar Tool Dibuang')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filter Data</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12 col-lg-4">
                        <label for="from_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="from_date">
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <label for="to_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="to_date">
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <label for="filter_bukti" class="form-label">Filter Bukti</label>
                        <select class="form-select" id="filter_bukti">
                            <option value="">Semua</option>
                            <option value="sudah_upload">Sudah Upload</option>
                            <option value="belum_upload">Belum Upload</option>
                        </select>
                    </div>
                    <div class="col-md-12 col-lg-4 d-flex align-items-end">
                        <button type="button" class="btn btn-primary me-2" id="btn-filter">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button type="button" class="btn btn-secondary" id="btn-reset">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Tool Dibuang</h5>
                        <div>
                            <button type="button" class="btn btn-success text-white me-2" id="btn-export">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <a href="{{ route('tool-dibuang.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tool-dibuang-table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Tanggal</th>
                                        <th>Nomor</th>
                                        <th>Kode Material</th>
                                        <th>Deskripsi</th>
                                        <th>Spesifikasi</th>
                                        <th>Detail Kerusakan</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Bukti -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tool-dibuang.upload-bukti') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload File Bukti</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="tool_dibuang_id" id="modal_tool_dibuang_id">
                        <div class="mb-3">
                            <label for="bukti_file" class="form-label">Pilih File Bukti (PDF/JPG/PNG)</label>
                            <input type="file" class="form-control" id="bukti_file" name="bukti_file" required
                                accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Maksimal ukuran file 2MB.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            function load_data(from_date = '', to_date = '', filter_bukti = '') {
                $('#tool-dibuang-table').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('tool-dibuang.datatable') }}",
                        data: {
                            from_date: from_date,
                            to_date: to_date,
                            filter_bukti: filter_bukti
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal_dibuang',
                            name: 'toolDibuang.tanggal_dibuang'
                        },
                        {
                            data: 'nomor_bs',
                            name: 'toolDibuang.nomor_bs',
                            searchable: true
                        },
                        {
                            data: 'kode_material',
                            name: 'kode_material',
                            searchable: true
                        },
                        {
                            data: 'deskripsi',
                            name: 'deskripsi',
                            searchable: true
                        },
                        {
                            data: 'spesifikasi',
                            name: 'spesifikasi',
                            searchable: true
                        },
                        {
                            data: 'detail_kerusakan',
                            name: 'detail_kerusakan',
                            searchable: true
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true
                        }
                    ]
                });
            }

            load_data();

            $('#btn-filter').click(function() {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var filter_bukti = $('#filter_bukti').val();
                load_data(from_date, to_date, filter_bukti);
            });

            $('#btn-reset').click(function() {
                $('#from_date').val('');
                $('#to_date').val('');
                $('#filter_bukti').val('');
                load_data();
            });

            $('#btn-export').click(function() {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var url = "{{ route('tool-dibuang.export') }}";
                var params = [];
                if (from_date) params.push('from_date=' + from_date);
                if (to_date) params.push('to_date=' + to_date);

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                window.location.href = url;
            });

            // Handle modal data passing
            $('#uploadModal').on('show.coreui.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);
                modal.find('#modal_tool_dibuang_id').val(id);
            });
        });
    </script>
@endpush
