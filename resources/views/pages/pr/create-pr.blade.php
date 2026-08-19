@extends('layouts.app')

@section('title', 'Buat Purchase Requisition Baru')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="card-title mb-0">Form Purchase Requisition</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pr.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">No. PR <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_pr" value="{{ old('no_pr') }}" required>
                        <small class="text-muted">Nomor Purchase Requisition (harus unik)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal PR <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_pr"
                            value="{{ old('tanggal_pr', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <hr>

                {{-- Detail Tools yang Rusak --}}
                <div class="mb-4">
                    <h6 class="mb-3">Pilih Tools yang Rusak untuk PR</h6>

                    @if (isset($availableTools) && count($availableTools) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th>No. SPR</th>
                                        <th>Tanggal SPR</th>
                                        <th>Kode Material</th>
                                        <th>Deskripsi</th>
                                        <th>Project</th>
                                        <th>Lokasi Tool</th>
                                        <th>Deskripsi Kerusakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($availableTools as $tool)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="detail_kerusakan_tools_ids[]"
                                                    value="{{ $tool->id }}" class="form-check-input tool-checkbox"
                                                    {{ in_array($tool->id, old('detail_kerusakan_tools_ids', [])) ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $tool->no_spr ?? '-' }}</td>
                                            <td>{{ $tool->tanggal_spr ? \Carbon\Carbon::parse($tool->tanggal_spr)->format('d/m/Y') : '-' }}
                                            <td>{{ $tool->detailDaftarDistribusiTool->daftarDistribusiTool->tool->kode_material ?? '-' }}
                                            </td>
                                            <td>{{ $tool->detailDaftarDistribusiTool->daftarDistribusiTool->tool->deskripsi ?? '-' }}
                                            </td>
                                            <td>{{ $tool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->project->nama_project ?? '-' }}
                                            </td>
                                            <td>{{ $tool->detailDaftarDistribusiTool->daftarDistribusiTool->tool->posisiCT->nama_posisi_ct ?? '-' }}
                                            </td>
                                            <td>
                                                <div class="text-wrap" style="max-width: 200px;">
                                                    {{ $tool->deskripsi_kerusakan }}
                                                </div>

                                            </td>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Informasi:</strong> Mohon Pilih tools yang rusak yang akan dimasukkan ke dalam Purchase
                            Requisition ini.
                            Hanya tools dengan status "Tidak Bisa Diperbaiki" yang dapat dimasukkan ke PR.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Tidak ada data:</strong> Tidak ada tools rusak yang tersedia untuk dimasukkan ke PR.
                            Pastikan ada tools dengan status "Tidak Bisa Diperbaiki" yang belum masuk PR.
                        </div>
                    @endif
                </div>

                <hr>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('pr.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan PR
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            // Select All functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const toolCheckboxes = document.querySelectorAll('.tool-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    toolCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Update select all when individual checkboxes change
            toolCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.tool-checkbox:checked').length;
                    const totalCount = toolCheckboxes.length;

                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = checkedCount === totalCount;
                        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount <
                            totalCount;
                    }
                });
            });

            // Form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const checkedTools = document.querySelectorAll('.tool-checkbox:checked');

                    if (checkedTools.length === 0) {
                        e.preventDefault();
                        alert('Pilih minimal satu tool untuk dimasukkan ke PR!');
                        return false;
                    }
                });
            }
        });
    </script>
@endpush
