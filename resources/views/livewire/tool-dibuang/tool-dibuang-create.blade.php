<div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Formulir Pendataan Tool Dibuang (Import Excel)</h5>
            <div wire:loading wire:target="save" class="text-muted">
                <span class="spinner-border spinner-border-sm" role="status"></span>
                Menyimpan...
            </div>
        </div>
        <div class="card-body">
            @if (session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="tanggal_dibuang" class="form-label">Tanggal Dibuang <span
                            class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('tanggal_dibuang') is-invalid @enderror"
                        id="tanggal_dibuang" wire:model="tanggal_dibuang">
                    @error('tanggal_dibuang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-8">
                    <label for="file_excel" class="form-label">Import File Excel <span
                            class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('file_excel') is-invalid @enderror" id="file_excel"
                        wire:model="file_excel" accept=".xlsx,.xls,.csv">
                    @error('file_excel')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div wire:loading wire:target="file_excel" class="text-muted small mt-1">
                        <span class="spinner-border spinner-border-sm" role="status"></span> Memproses file...
                    </div>
                </div>
            </div>

            @if (session()->has('success_import'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success_import') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error_import'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error_import') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (!empty($importErrors))
                <div class="alert alert-danger">
                    <h6 class="alert-heading">Daftar Kesalahan Import:</h6>
                    <ul class="mb-0">
                        @foreach ($importErrors as $error)
                            <li>Baris {{ $error['row'] }} ({{ $error['kode_material'] ?? 'N/A' }}):
                                {{ implode(', ', $error['errors']) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($daftarTool))
                <h5 class="mb-3">Preview Data Import</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">No</th>
                                <th>Kode Material</th>
                                <th>Deskripsi</th>
                                <th>Spesifikasi</th>
                                <th>Detail Kerusakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftarTool as $index => $item)
                                <tr wire:key="item-{{ $index }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['kode_material'] }}</td>
                                    <td>{{ $item['deskripsi'] }}</td>
                                    <td>{{ $item['spesifikasi'] }}</td>
                                    <td>{{ $item['detail_kerusakan'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('items')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            @endif
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('tool-dibuang.index') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="button" wire:click="save" class="btn btn-primary" wire:loading.attr="disabled"
                @if (empty($daftarTool) || !empty($importErrors)) disabled @endif>
                <span wire:loading.remove wire:target="save">
                    Simpan Data
                </span>
                <span wire:loading wire:target="save">
                    Menyimpan...
                </span>
            </button>
        </div>
    </div>
</div>
