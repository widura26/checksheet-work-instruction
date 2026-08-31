@extends('layouts.app')

@section('title', 'Daftar Karyawan')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }

        #karyawan-table {
            width: 100% !important;
        }

        .icon {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Data Karyawan</h5>
            </div>
            @if (Auth::user()->hasRole(['admin']))
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-success" type="button" id="btnImportExcel">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M13 2v9h7l-8 11v-9h-7z"></path></svg>
                        Import Excel
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="columnToggleBtn"
                            data-coreui-toggle="dropdown" aria-expanded="false">
                            <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 5h18v2H3zM3 11h18v2H3zM3 17h18v2H3z"></path></svg>
                            Kolom
                        </button>
                        <ul class="dropdown-menu p-2" id="columnToggleMenu"
                            style="min-width: 220px; max-height: 320px; overflow-y: auto;">
                            <li class="text-muted small px-2 pb-1">Tampilkan / sembunyikan kolom</li>
                        </ul>
                    </div>
                    <button class="btn btn-primary" type="button" id="btnCreate">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg>
                        Tambah Karyawan
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="status_filter">
                        <option value="">Semua</option>
                        <option value="kosong">Belum Lengkap</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search_input"
                            placeholder="Cari NIP, Nama, Jabatan, Departemen, Bagian, Unit...">
                        <button class="btn btn-primary" type="button" id="btnSearch">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                        <button class="btn btn-secondary" type="button" id="btnReset">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="karyawan-table" class="table table-bordered table-hover" style="width: 100%;">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Bagian</th>
                            <th>Unit</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Karyawan Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Karyawan</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create_nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="create_nip" name="nip" required>
                        </div>
                        <div class="mb-3">
                            <label for="create_nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="create_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="create_departemen_id" class="form-label">Departemen</label>
                            <select class="form-select departemen-select" id="create_departemen_id" name="departemen_id">
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="create_bagian_id" class="form-label">Bagian</label>
                            <select class="form-select bagian-select" id="create_bagian_id" name="bagian_id">
                                <option value="">Pilih Bagian</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="create_unit_id" class="form-label">Unit</label>
                            <select class="form-select unit-select" id="create_unit_id" name="unit_id">
                                <option value="">Pilih Unit</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="create_jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="create_jabatan" name="jabatan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btnCreateSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Karyawan Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="nip" id="edit_nip">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Karyawan</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nip_display" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="edit_nip_display" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_departemen_id" class="form-label">Departemen</label>
                            <select class="form-select departemen-select" id="edit_departemen_id" name="departemen_id">
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_bagian_id" class="form-label">Bagian</label>
                            <select class="form-select bagian-select" id="edit_bagian_id" name="bagian_id">
                                <option value="">Pilih Bagian</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_unit_id" class="form-label">Unit</label>
                            <select class="form-select unit-select" id="edit_unit_id" name="unit_id">
                                <option value="">Pilih Unit</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="edit_jabatan" name="jabatan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btnEditSubmit">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Karyawan Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Karyawan</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="import_file" class="form-label">File Excel</label>
                            <input type="file" class="form-control" id="import_file" name="file"
                                accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Format yang didukung: .xlsx, .xls, .csv</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success" id="btnImportSubmit">Import Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.Karyawan = {
            routes: {
                data: "{{ route('karyawan.data') }}",
                store: "{{ route('karyawan.store') }}",
                show: "{{ route('karyawan.show', '__NIP__') }}",
                update: "{{ route('karyawan.update', '__NIP__') }}",
                destroy: "{{ route('karyawan.destroy', '__NIP__') }}",
                import: "{{ route('karyawan.import') }}",
                bagian: "{{ route('distribusi.api.bagian', '__ID__') }}",
                unit: "{{ route('distribusi.api.unit', '__ID__') }}",
            },
            canManage: {{ Auth::user()->hasRole(['admin']) ? 'true' : 'false' }}
        };
    </script>
    <script type="module" src="{{ asset('js/karyawan/index.js') }}"></script>
@endpush
