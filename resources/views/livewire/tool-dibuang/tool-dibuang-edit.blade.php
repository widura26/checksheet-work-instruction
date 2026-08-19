<div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Formulir Edit Tool Dibuang : {{ $noBs }}</h5>
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
            <div class="div">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-success text-white" wire:click="addRow">Tambah Baris</button>
                </div>
                <table class="table table-striped table-hover">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Kode Material</th>
                        <th>Deskripsi</th>
                        <th>Spesifikasi</th>
                        <th>Detail Kerusakan</th>
                        <th>Action</th>
                    </tr>
                    @foreach ($daftarToolDibuang as $index => $toolDibuang)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <input type="text"
                                    wire:model.defer="daftarToolDibuang.{{ $index }}.kode_material"class="form-control">
                            </td>

                            <td>
                                <input type="text" wire:model.defer="daftarToolDibuang.{{ $index }}.deskripsi"
                                    class="form-control">
                            </td>

                            <td>
                                <input type="text"
                                    wire:model.defer="daftarToolDibuang.{{ $index }}.spesifikasi"
                                    class="form-control">
                            </td>

                            <td>
                                <textarea wire:model.defer="daftarToolDibuang.{{ $index }}.detail_kerusakan" rows="4" class="form-control">{{ $toolDibuang['detail_kerusakan'] }}</textarea>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger text-white"
                                    wire:click="delete({{ $index }})">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('tool-dibuang.index') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="button" wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
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
