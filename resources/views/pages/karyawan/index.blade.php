@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Data Karyawan</h5>
                    @if (Auth::user()->hasRole(['admin']))
                        <button type="button" class="btn btn-primary" data-coreui-toggle="modal"
                            data-coreui-target="#createKaryawanModal">
                            Tambah Karyawan
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-start alert-dismissible fade show" role="alert">
                            <svg class="icon me-2 mt-1">
                                <use xlink:href="{{ asset('vendors/@coreui/icons/svg/free.svg#cil-warning') }}"></use>
                            </svg>
                            <div>
                                <strong>Validasi gagal:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="row mb-3">
                        @if (Auth::user()->hasRole('admin'))
                            <form action="{{ route('karyawan.import') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls">
                                    <button type="submit" class="btn btn-success text-white">Import Data</button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="form-search mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('karyawan.index', ['status' => 'kosong']) }}" class="btn btn-primary">Daftar Belum
                            Lengkap</a>
                        <form action="{{ route('karyawan.index') }}" method="get">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari..."
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-success text-white">Cari</button>
                                <a href="{{ route('karyawan.index') }}" class="btn btn-danger text-white ms-2">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
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
                            <tbody>
                                @forelse ($karyawans as $karyawan)
                                    <tr>
                                        <td>{{ $loop->iteration + ($karyawans->currentPage() - 1) * $karyawans->perPage() }}
                                        </td>
                                        <td>{{ $karyawan->nip }}</td>
                                        <td>{{ $karyawan->nama }}</td>
                                        <td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                                        <td>{{ $karyawan->bagian->nama_bagian ?? '-' }}</td>
                                        <td>{{ $karyawan->unit->nama_unit ?? '-' }}</td>
                                        <td>{{ $karyawan->jabatan }}</td>
                                        <td class="d-flex justify-content-center gap-1 align-items-center">
                                            <button type="button" class="btn btn-sm btn-warning" data-coreui-toggle="modal"
                                                data-coreui-target="#editKaryawanModal-{{ $karyawan->nip }}">
                                                Edit
                                            </button>
                                            <form action="{{ route('karyawan.destroy', $karyawan->nip) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data karyawan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            {{ $karyawans->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($karyawans as $karyawan)
        <div class="modal fade {{ $errors->any() && old('nip') == $karyawan->nip ? 'show' : '' }}"
            id="editKaryawanModal-{{ $karyawan->nip }}" tabindex="-1"
            aria-labelledby="editKaryawanModalLabel-{{ $karyawan->nip }}" aria-hidden="true"
            @if ($errors->any() && old('nip') == $karyawan->nip) style="display: block;" @endif>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editKaryawanModalLabel-{{ $karyawan->nip }}">Edit Karyawan:
                            {{ $karyawan->nama }}</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('karyawan.update', $karyawan->nip) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_nip_{{ $karyawan->nip }}" class="form-label">NIP</label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror"
                                    id="edit_nip_{{ $karyawan->nip }}" name="nip"
                                    value="{{ old('nip', $karyawan->nip) }}" required>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_nama_{{ $karyawan->nip }}" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="edit_nama_{{ $karyawan->nip }}" name="nama"
                                    value="{{ old('nama', $karyawan->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_departemen_id_{{ $karyawan->nip }}" class="form-label">Departemen</label>
                                <select class="form-select departemen-select @error('departemen_id') is-invalid @enderror"
                                    id="edit_departemen_id_{{ $karyawan->nip }}" name="departemen_id">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departemens as $departemen)
                                        <option value="{{ $departemen->id }}"
                                            {{ old('departemen_id', $karyawan->departemen_id) == $departemen->id ? 'selected' : '' }}>
                                            {{ $departemen->nama_departemen }}</option>
                                    @endforeach
                                </select>
                                @error('departemen_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_bagian_id_{{ $karyawan->nip }}" class="form-label">Bagian</label>
                                <select class="form-select bagian-select @error('bagian_id') is-invalid @enderror"
                                    id="edit_bagian_id_{{ $karyawan->nip }}" name="bagian_id">
                                    <option value="">Pilih Bagian</option>
                                    @foreach ($bagians as $bagian)
                                        @if ($bagian->departemen_id == old('departemen_id', $karyawan->departemen_id))
                                            <option value="{{ $bagian->id }}"
                                                {{ old('bagian_id', $karyawan->bagian_id) == $bagian->id ? 'selected' : '' }}>
                                                {{ $bagian->nama_bagian }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('bagian_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_unit_id_{{ $karyawan->nip }}" class="form-label">Unit</label>
                                <select class="form-select unit-select @error('unit_id') is-invalid @enderror"
                                    id="edit_unit_id_{{ $karyawan->nip }}" name="unit_id">
                                    <option value="">Pilih Unit</option>
                                    @foreach ($units as $unit)
                                        @if ($unit->bagian_id == old('bagian_id', $karyawan->bagian_id))
                                            <option value="{{ $unit->id }}"
                                                {{ old('unit_id', $karyawan->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->nama_unit }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_jabatan_{{ $karyawan->nip }}" class="form-label">Jabatan</label>
                                <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                    id="edit_jabatan_{{ $karyawan->nip }}" name="jabatan"
                                    value="{{ old('jabatan', $karyawan->jabatan) }}" required>
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Create Karyawan Modal -->
    <div class="modal fade " id="createKaryawanModal" tabindex="-1" aria-labelledby="createKaryawanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createKaryawanModalLabel">Tambah Karyawan Baru</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('karyawan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip"
                                name="nip" value="{{ old('nip') }}" required>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="departemen_id" class="form-label">Departemen</label>
                            <select class="form-select departemen-select @error('departemen_id') is-invalid @enderror"
                                id="departemen_id" name="departemen_id">
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id }}"
                                        {{ old('departemen_id') == $departemen->id ? 'selected' : '' }}>
                                        {{ $departemen->nama_departemen }}</option>
                                @endforeach
                            </select>
                            @error('departemen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bagian_id" class="form-label">Bagian</label>
                            <select class="form-select bagian-select @error('bagian_id') is-invalid @enderror"
                                id="bagian_id" name="bagian_id">
                                <option value="">Pilih Bagian</option>
                                @foreach ($bagians as $bagian)
                                    @if (old('departemen_id') && $bagian->departemen_id == old('departemen_id'))
                                        <option value="{{ $bagian->id }}"
                                            {{ old('bagian_id') == $bagian->id ? 'selected' : '' }}>
                                            {{ $bagian->nama_bagian }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('bagian_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Unit</label>
                            <select class="form-select unit-select @error('unit_id') is-invalid @enderror" id="unit_id"
                                name="unit_id">
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    @if (old('bagian_id') && $unit->bagian_id == old('bagian_id'))
                                        <option value="{{ $unit->id }}"
                                            {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->nama_unit }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
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
            // Handle Departemen Change
            $(document).on('change', '.departemen-select', function() {
                const departemenId = $(this).val();
                const $form = $(this).closest('form');
                const $bagianSelect = $form.find('.bagian-select');
                const $unitSelect = $form.find('.unit-select');

                // Clear Bagian and Unit
                $bagianSelect.empty().append('<option value="">Pilih Bagian</option>');
                $unitSelect.empty().append('<option value="">Pilih Unit</option>');

                if (departemenId) {
                    let url = "{{ route('distribusi.api.bagian', ':id') }}";
                    url = url.replace(':id', departemenId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $bagianSelect.append('<option value="' + value.id +
                                    '">' +
                                    value.nama_bagian + '</option>');
                            });
                        },
                        error: function(xhr) {}
                    });
                }
            });

            // Handle Bagian Change
            $(document).on('change', '.bagian-select', function() {
                const bagianId = $(this).val();
                const $form = $(this).closest('form');
                const $unitSelect = $form.find('.unit-select');

                // Clear Unit
                $unitSelect.empty().append('<option value="">Pilih Unit</option>');

                if (bagianId) {
                    let url = "{{ route('distribusi.api.unit', ':id') }}";
                    url = url.replace(':id', bagianId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $unitSelect.append('<option value="' + value.id + '">' +
                                    value.nama_unit + '</option>');
                            });
                        },
                        error: function(xhr) {}
                    });
                }
            });
        });
    </script>
@endpush
