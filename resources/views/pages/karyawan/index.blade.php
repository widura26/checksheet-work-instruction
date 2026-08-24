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
                    @if (Auth::user()->hasRole('admin'))
                        <div class="row mb-3">
                            <form action="{{ route('karyawan.import') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls">
                                    <button type="submit" class="btn btn-success text-white">Import Data</button>
                                </div>
                            </form>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" name="status_filter" id="status_filter">
                                <option value="">Semua</option>
                                <option value="kosong">Belum Lengkap</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <button id="filter-button" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>
                                Filter
                            </button>
                            <button id="reset-filter-button" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="table-karyawan" class="table table-bordered table-hover">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Edit Karyawan Modal -->
    <div class="modal fade" id="editKaryawanModal" tabindex="-1" aria-labelledby="editKaryawanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editKaryawanModalLabel">Edit Karyawan</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editKaryawanForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="nip" id="edit_nip">
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
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            var table = $('#table-karyawan').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                ajax: function(data, callback, settings) {
                    $.get("{{ route('karyawan.data') }}", {
                        page: (data.start / data.length) + 1,
                        per_page: data.length,
                        search: data.search.value,
                        status: $('#status_filter').val()
                    }).done(function(json) {
                        callback({
                            draw: data.draw,
                            recordsTotal: json.total,
                            recordsFiltered: json.total,
                            data: json.data
                        });
                    });
                },
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'nip',
                        name: 'nip'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'bagian',
                        name: 'bagian'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ]
            });

            $('#filter-button').click(function() {
                table.ajax.reload();
            });

            $('#reset-filter-button').click(function() {
                $('#status_filter').val('');
                table.ajax.reload();
            });

            function loadOptions(url, target, selected, labelField, cb) {
                $.getJSON(url, function(data) {
                    var $sel = $(target);
                    $sel.empty().append('<option value="">Pilih</option>');
                    $.each(data, function(i, v) {
                        $sel.append('<option value="' + v.id + '">' + v[labelField] + '</option>');
                    });
                    if (selected) {
                        $sel.val(selected);
                    }
                    if (cb) {
                        cb();
                    }
                });
            }

            window.openEditKaryawan = function(nip) {
                var url = "{{ route('karyawan.show', ':nip') }}".replace(':nip', nip);
                $.getJSON(url, function(k) {
                    $('#edit_nip').val(k.nip);
                    $('#edit_nip_display').val(k.nip);
                    $('#edit_nama').val(k.nama);
                    $('#edit_jabatan').val(k.jabatan);
                    $('#edit_departemen_id').val(k.departemen_id);
                    $('#editKaryawanForm').attr('action',
                        "{{ route('karyawan.update', ':nip') }}".replace(':nip', k.nip));

                    loadOptions("{{ route('distribusi.api.bagian', ':id') }}".replace(':id', k.departemen_id),
                        '#edit_bagian_id', k.bagian_id, 'nama_bagian', function() {
                            loadOptions("{{ route('distribusi.api.unit', ':id') }}".replace(':id', k.bagian_id),
                                '#edit_unit_id', k.unit_id, 'nama_unit');
                        });

                    new coreui.Modal(document.getElementById('editKaryawanModal')).show();
                });
            };

            // Handle Departemen Change
            $(document).on('change', '.departemen-select', function() {
                const departemenId = $(this).val();
                const $form = $(this).closest('form');
                const $bagianSelect = $form.find('.bagian-select');
                const $unitSelect = $form.find('.unit-select');

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
