@extends('layouts.app')

@section('title', 'Konversi Satuan')

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Konversi Satuan</h5>
                    <button type="button" class="btn btn-primary" id="btnTambah">
                        <i class="cil-plus me-1"></i> Tambah Konversi
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="konversiTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kode Material</th>
                                    <th>Satuan Distribusi</th>
                                    <th>Berat Per Satuan Distribusi (Gram)</th>
                                    <th>Nilai Konversi</th>
                                    <th>Satuan SAP</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($konversi as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->kode_material }}</td>
                                        <td>{{ $item->satuan_distribusi }}</td>
                                        <td>{{ $item->berat_per_satuan_distribusi }}</td>
                                        <td>{{ (float) $item->nilai_konversi }}</td>
                                        <td>{{ $item->satuan_sap }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm edit-btn"
                                                data-id="{{ $item->id }}" data-kode="{{ $item->kode_material }}"
                                                data-sap="{{ $item->satuan_sap }}"
                                                data-distribusi="{{ $item->satuan_distribusi }}"
                                                data-berat="{{ $item->berat_per_satuan_distribusi }}">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                data-id="{{ $item->id }}">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="konversiModal" tabindex="-1" aria-labelledby="konversiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="konversiForm" method="POST">
                    @csrf
                    <div id="method_field"></div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="konversiModalLabel">Form Konversi Satuan</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="kode_material" class="form-label">Kode Material <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="kode_material" id="kode_material"
                                        required placeholder="Masukkan Kode Material">
                                    <button class="btn btn-outline-secondary" type="button"
                                        id="btnCariMaterial">Cari</button>
                                </div>
                                <small class="text-muted">Masukkan kode material SAP untuk mengambil data otomatis.</small>
                            </div>
                        </div>

                        <div id="materialInfo" class="p-3 mb-3 bg-light border rounded d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Deskripsi:</strong> <span id="info_deskripsi">-</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Spesifikasi:</strong> <span id="info_spesifikasi">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="satuan_sap" class="form-label">Satuan SAP<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" name="satuan_sap" id="satuan_sap"
                                    required readonly>
                                <small class="text-muted">Satuan resmi dari sistem SAP.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="satuan_distribusi" class="form-label">Satuan Distribusi<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="satuan_distribusi" id="satuan_distribusi"
                                    required placeholder="Contoh: Pcs, Lembar">
                                <small class="text-muted">Satuan saat distribusi di lapangan.</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="berat_per_satuan_distribusi" class="form-label">Berat Per Satuan Distribusi
                                    (Gram)<span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" class="form-control"
                                    name="berat_per_satuan_distribusi" id="berat_per_satuan_distribusi" required>
                                <small class="text-muted">Misal 1 PCS = 12 Gram</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {
            const modalElement = document.getElementById('konversiModal');
            const modal = new coreui.Modal(modalElement);
            const form = $('#konversiForm');

            function fetchMaterial(kode) {
                if (!kode) return;

                $.ajax({
                    url: "{{ route('daftar-kode-material.find') }}",
                    type: "GET",
                    data: {
                        kode_material: kode
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#materialInfo').removeClass('d-none');
                            $('#info_deskripsi').text(response.data.deskripsi || '-');
                            $('#info_spesifikasi').text(response.data.spesifikasi || '-');
                            $('#satuan_sap').val(response.data.satuan);
                        } else {
                            $('#materialInfo').addClass('d-none');
                            Swal.fire('Oops!', 'Material tidak ditemukan', 'warning');
                            $('#satuan_sap').val('');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghubungi server', 'error');
                    }
                });
            }

            $('#btnCariMaterial').click(function() {
                fetchMaterial($('#kode_material').val());
            });

            $('#kode_material').on('blur', function() {
                fetchMaterial($(this).val());
            });

            // Add Button
            $('#btnTambah').click(function() {
                form.attr('action', "{{ route('konversi-satuan.store') }}");
                $('#method_field').html('');
                form[0].reset();
                $('#materialInfo').addClass('d-none');
                $('#konversiModalLabel').text('Tambah Konversi Satuan');
                $('#kode_material').prop('readonly', false);
                modal.show();
            });

            // Edit Button
            $('.edit-btn').click(function() {
                const updateUrl = "{{ route('konversi-satuan.update', ':id') }}";
                const data = $(this).data();
                form.attr('action', updateUrl.replace(':id', data.id));
                $('#method_field').html('@method('PUT')');

                $('#kode_material').val(data.kode).prop('readonly', true);
                $('#satuan_sap').val(data.sap);
                $('#satuan_distribusi').val(data.distribusi);
                $('#berat_per_satuan_distribusi').val(data.berat);

                $('#konversiModalLabel').text('Edit Konversi Satuan');

                // Fetch material info for display
                fetchMaterial(data.kode);
                modal.show();
            });

            $('.delete-btn').click(function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data konversi ini akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('konversi-satuan.destroy', ':id') }}".replace(
                                ':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Terhapus!', response.success, 'success')
                                    .then(() => {
                                        location.reload();
                                    });
                            },
                            error: function() {
                                Swal.fire('Gagal!',
                                    'Terjadi kesalahan saat menghapus data.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
