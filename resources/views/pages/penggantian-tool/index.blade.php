@extends('layouts.app')

@section('title', 'Riwayat Penggantian Tools')

@push('css')
@endpush

@section('content')
    @if (session('success'))
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Riwayat Penggantian Tools</h5>
            <small class="text-body-secondary">Halaman ini menampilkan data penggantian tools</small>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('penggantian-tool.create') }}" class="btn btn-success text-white">Tambah Penggantian</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="penggantian-tool-table">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Qty Diganti</th>
                            <th>Tanggal Penggantian</th>
                            <th>Status</th>
                            <th>Unit</th>
                            <th>Bukti Kerusakan</th>
                            <th class="text-center">Bukti Penggantian</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('penggantian-tool.update-status') }}"
                            enctype="multipart/form-data" class="modal-content">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Status</h5>
                                <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="rowId">
                                <div class="mb-3">
                                    <label class="form-label">Upload Bukti Penyerahan</label>
                                    <input type="file" name="file_bukti" class="form-control"
                                        accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">
                                    Batal
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            $('#penggantian-tool-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('penggantian-tool.data') }}",
                },
                order: [
                    [4, 'desc']
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
                        name: 'tool.kode_material',
                        orderable: false,
                    },
                    {
                        data: 'tool',
                        name: 'tool.deskripsi',
                        orderable: false,
                    },
                    {
                        data: 'qty_diganti',
                        name: 'qty_diganti'
                    },
                    {
                        data: 'tanggal_penggantian',
                        name: 'tanggal_penggantian'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                        orderable: false,
                    },
                    {
                        data: 'file',
                        name: 'file',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                    },
                ],
            });
            document.getElementById('updateStatusModal')
                .addEventListener('show.coreui.modal', function(event) {

                    let button = event.relatedTarget;

                    let id = button.getAttribute('data-id');
                    let status = button.getAttribute('data-status');

                    document.getElementById('rowId').value = id;
                    document.getElementById('statusSelect').value = status;
                });

        });
    </script>
@endpush
