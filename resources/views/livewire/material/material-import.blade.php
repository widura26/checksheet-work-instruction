<div class="flex-grow-1 min-w-0">
    <form action="{{ route('import-material') }}" method="post" enctype="multipart/form-data" class="mb-0">
        @csrf
        <div class="input-group">
            <input type="file" name="file" wire:model.live="file_excel" class="form-control" accept=".xlsx,.xls">
            <button type="submit" class="btn btn-success text-white" @if ($file_excel && !empty($importErrors)) disabled @endif>
                <i class="fas fa-file-import me-1"></i> Import Material
            </button>
        </div>

        <div wire:loading wire:target="file_excel" class="mt-2 text-primary">
            <div class="spinner-border spinner-border-sm me-1" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Memproses file
        </div>

        @if (session()->has('success_preview'))
            <div class="alert alert-success mt-3 py-2">
                <i class="fas fa-check-circle me-1"></i> {{ session('success_preview') }}
            </div>
        @endif

        @if (session()->has('error_preview'))
            <div class="alert alert-danger mt-3 py-2">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error_preview') }}
            </div>
        @endif

        @error('file_excel')
            <div class="text-danger mt-1 small">{{ $message }}</div>
        @enderror

        {{-- Preview Errors --}}
        @if (!empty($importErrors))
            <div class="card border-danger mt-3">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-bold">Data Gagal Validasi (Preview)</h6>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-striped table-bordered mb-0">
                        <thead class="table-danger sticky-top">
                            <tr>
                                <th class="text-center" style="width: 80px;">Baris</th>
                                <th style="width: 180px;">Kode Material</th>
                                <th>Pesan Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($importErrors as $error)
                                <tr>
                                    <td class="text-center">{{ $error['row'] }}</td>
                                    <td>{{ $error['kode_material'] ?? '-' }}</td>
                                    <td class="text-danger small">{{ $error['error'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-1 bg-light">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                        Total Error: {{ count($importErrors) }}
                    </small>
                </div>
            </div>
        @endif

        {{-- Preview Data --}}
        @if (!empty($previewData))
            <div class="card mt-3 border-info">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0 fw-bold">Preview Isi Excel (10 Baris Pertama)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th>Kode Material</th>
                                    <th>Project</th>
                                    <th>Posisi CT</th>
                                    <th class="text-center">Total</th>
                                    <th>Vendor</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($previewData as $row)
                                    <tr class="small">
                                        <td>{{ $row['kode_material'] ?? '-' }}</td>
                                        <td>{{ $row['project_id'] ?? '-' }}</td>
                                        <td>{{ $row['posisi_ct'] ?? '-' }}</td>
                                        <td class="text-center">{{ $row['total'] ?? '-' }}</td>
                                        <td>{{ $row['vendor'] ?? '-' }}</td>
                                        <td>{{ $row['kategori'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>
