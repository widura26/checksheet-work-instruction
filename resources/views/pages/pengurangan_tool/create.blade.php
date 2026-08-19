@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Form Pengurangan Stok Tool</h5>
            {{-- @if (!isset($publicId)) --}}
            {{-- <button type="button" class="btn btn-success btn-sm text-white" id="add-tool-row">
                    <svg class="icon">
                        <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-plus') }}"></use>
                    </svg> Tambah Tool
                </button> --}}
            {{-- @endif --}}
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('pengurangan-tool.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($publicId))
                    <input type="hidden" name="public_id_detail_kerusakan_tool" value="{{ $publicId }}">
                    <input type="hidden" name="project_id" value="{{ $projectId }}">
                    <input type="hidden" name="tool_id" value="{{ $tool_id }}">
                    <input type="hidden" name="posisi_ct_id" value="{{ $posisi_ct_id }}">
                @endif
                {{-- Dynamic Tool Rows --}}
                <div id="tool-rows-container">
                    {{-- JavaScript will populate this --}}
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Pengurangan</button>
                    <a href="{{ route('pengurangan-tool.index') }}" class="btn btn-secondary ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('tool-rows-container');
            const addRowButton = document.getElementById('add-tool-row');
            let rowIndex = 0;
            const prefilledData = @json($toolData);

            function createRow(data = null) {
                const newRow = document.createElement('div');
                newRow.classList.add('tool-row', 'border', 'rounded', 'p-3', 'mb-4');
                newRow.setAttribute('data-index', rowIndex);

                newRow.innerHTML = `
            <h5>Tool #${rowIndex + 1}</h5>
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Kode Material <span class="text-danger">*</span></label>
                    <input type="text" class="form-control kode-material" name="tools[${rowIndex}][kode_material]" required>
                    ( Tekan Tab Setelah Mengisi Kode Material)
                    <div class="invalid-feedback"></div>

                </div>
                <div class="col-md-3">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" class="form-control deskripsi" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stok</label>
                    <input type="text" class="form-control qty-current" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Qty Kurang <span class="text-danger">*</span></label>
                    <input type="number" readonly class="form-control qty-dikurangi" name="tools[${rowIndex}][qty_dikurangi]" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Satuan</label>
                    <input type="text" class="form-control satuan" readonly>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Alasan Pengurangan</label>
                    <textarea class="form-control" name="tools[${rowIndex}][alasan]" rows="2"></textarea>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Dokumen <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="tools[${rowIndex}][document]" required>
                </div>
            </div>
             <div class="text-center ms-auto me-auto col-4 mt-4">
                    <button type="button" class="btn btn-danger remove-tool-row w-100 text-white">Hapus</button>
                </div>
        `;

                container.appendChild(newRow);

                if (data) {
                    const kodeMaterialInput = newRow.querySelector('.kode-material');
                    const deskripsiInput = newRow.querySelector('.deskripsi');
                    const qtyCurrentInput = newRow.querySelector('.qty-current');
                    const satuanInput = newRow.querySelector('.satuan');
                    const qtyDikurangiInput = newRow.querySelector('.qty-dikurangi');
                    kodeMaterialInput.value = data.kode_material || '';
                    deskripsiInput.value = data.deskripsi || '';
                    qtyCurrentInput.value = data.stok || 0;
                    satuanInput.value = data.satuan || '';
                    qtyDikurangiInput.value = 1;
                    // Make the field readonly if it's pre-filled
                    kodeMaterialInput.readOnly = true;
                }

                rowIndex++;
            }

            // addRowButton.addEventListener('click', function() {
            //     createRow();
            // });

            container.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-tool-row')) {
                    e.target.closest('.tool-row').remove();
                }
            });

            container.addEventListener('blur', function(e) {
                if (e.target && e.target.classList.contains('kode-material') && !e.target.readOnly) {
                    const row = e.target.closest('.tool-row');
                    const kodeMaterial = e.target.value;

                    const deskripsiInput = row.querySelector('.deskripsi');
                    const qtyCurrentInput = row.querySelector('.qty-current');
                    const satuanInput = row.querySelector('.satuan');
                    const invalidFeedback = row.querySelector('.invalid-feedback');

                    // Reset fields
                    deskripsiInput.value = '';
                    qtyCurrentInput.value = '';
                    satuanInput.value = '';
                    e.target.classList.remove('is-invalid');
                    invalidFeedback.textContent = '';

                    if (kodeMaterial) {
                        $.ajax({
                            url: `{{ route('pengurangan-tool.get-tool-details', ':kode') }}`
                                .replace(':kode', kodeMaterial),
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                deskripsiInput.value = data.deskripsi || '';
                                qtyCurrentInput.value = data.qty || 0;
                                satuanInput.value = data.satuan || '';

                                // Hapus invalid state jika sebelumnya error
                                e.target.classList.remove('is-invalid');
                                invalidFeedback.textContent = '';
                            },
                            error: function(xhr) {
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                e.target.classList.add('is-invalid');
                                invalidFeedback.textContent = "Tool Tidak Ditemukan";
                            }
                        });
                    }
                }
            }, true);

            if (prefilledData) {
                createRow(prefilledData);
            } else {
                createRow();
            }
        });
    </script>
@endpush
