<div>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Peminjaman</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nip_peminjam" class="form-label">NIP Peminjam ( Tekan Enter untuk mencari data
                            )</label>
                        <input type="text" class="form-control @error('nip_peminjam') is-invalid @enderror"
                            id="nip_peminjam" wire:keydown.enter.prevent="searchNipData" wire:model.live="nip_peminjam"
                            placeholder="NIP Peminjam">
                        @error('nip_peminjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nama_peminjam" class="form-label">Nama Peminjam</label>
                        <input type="text" id="nama_peminjam"
                            class="form-control @error('nama_peminjam') is-invalid @enderror"
                            wire:model.defer="nama_peminjam" placeholder="Nama Peminjam">
                        @error('nama_peminjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="departemen_id" class="form-label">Departemen</label>
                        <select class="form-select @error('departemen_id') is-invalid @enderror" id="departemen_id"
                            wire:model.live="departemen_id">
                            <option value="">Pilih Departemen</option>
                            @foreach ($departemens as $departemen)
                                <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                            @endforeach
                        </select>
                        @error('departemen_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="bagian_id" class="form-label">Bagian</label>
                        <select class="form-select @error('bagian_id') is-invalid @enderror"
                            wire:key="bagian-{{ $departemen_id }}" id="bagian_id" wire:model.live="bagian_id"
                            @if ($bagians->isEmpty()) disabled @endif>
                            <option value="">Pilih Bagian</option>
                            @foreach ($bagians as $bagian)
                                <option value="{{ $bagian->id }}">{{ $bagian->nama_bagian }}</option>
                            @endforeach
                        </select>
                        @error('bagian_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="unit_id" class="form-label">Unit</label>
                        <select class="form-select @error('unit_id') is-invalid @enderror"
                            wire:key="unit-{{ $bagian_id }}" id="unit_id" wire:model.live="unit_id"
                            @if ($units->isEmpty()) disabled @endif>
                            <option value="">Pilih Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="project_id" class="form-label">Pilih Asal Project :</label>
                        <select class="form-select @error('project_id') is-invalid @enderror" id="project_id"
                            wire:model.live="project_id">
                            <option value="">Pilih Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->project_id }}"
                                    @if ($project->project_id == $project_id_dest) disabled @endif>{{ $project->nama_project }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="project_id_dest" class="form-label">Pilih Untuk Project :</label>
                        <select class="form-select @error('project_id_dest') is-invalid @enderror" id="project_id_dest"
                            wire:model.live="project_id_dest">
                            <option value="">Pilih Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->project_id }}"
                                    @if ($project->project_id == $project_id) disabled @endif>{{ $project->nama_project }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id_dest')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" id="keterangan"
                            class="form-control @error('keterangan') is-invalid @enderror" wire:model.defer="keterangan"
                            placeholder="Opsional">
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <hr>
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 me-2">Daftar Material</h5>
                    <small class="text-muted">( Tekan Tab Setelah Mengisi Kode Material )</small>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="tool_table">
                        <thead>
                            <tr>
                                <th>Kode Material</th>
                                {{-- <th>Posisi</th> --}}
                                <th>Deskripsi</th>
                                <th>Spesifikasi</th>
                                <th>Stok Tersedia</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tools as $index => $tool)
                                <tr id="row{{ $index }}" wire:key="tool-row-{{ $index }}">
                                    <td>
                                        <input type="text" wire:model="tools.{{ $index }}.kode_material"
                                            wire:keydown.enter.prevent="onKodeMaterialChanged({{ $index }})"
                                            class="form-control @error('tools.' . $index . '.kode_material') is-invalid @enderror"
                                            id="kode_material_{{ $index }}">
                                        @error('tools.' . $index . '.kode_material')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    {{-- <td>
                                           tidak bisa memilih ct posisi karena sudah dikunci dengan departemen
                                        @if (!empty($tool['available_posisi']))
                                            <select class="form-select"
                                                wire:model.live="tools.{{ $index }}.posisi_ct_id">
                                                <option value="">Pilih Posisi</option>
                                                @foreach ($tool['available_posisi'] as $posisi)
                                                    <option value="{{ $posisi['posisi_ct_id'] }}">
                                                        {{ $posisi['nama_posisi_ct'] }} (stok: {{ $posisi['qty'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="form-control" readonly>
                                        @endif
                                    </td> --}}
                                    <td><input type="text" class="form-control"
                                            wire:model.defer="tools.{{ $index }}.deskripsi" readonly></td>
                                    <td><input type="text" class="form-control"
                                            wire:model.defer="tools.{{ $index }}.spesifikasi" readonly></td>
                                    <td class="col-1"><input type="text" class="form-control"
                                            wire:model.defer="tools.{{ $index }}.stok_tersedia" readonly></td>
                                    <td>
                                        <input type="number" id="qty_{{ $index }}"
                                            wire:model.defer="tools.{{ $index }}.qty"
                                            wire:keydown.enter.prevent="addTool"
                                            class="form-control @error('tools.' . $index . '.qty') is-invalid @enderror"
                                            min="1">
                                        @error('tools.' . $index . '.qty')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="col-1"><input type="text" class="form-control"
                                            wire:model.defer="tools.{{ $index }}.satuan" readonly></td>
                                    <td>
                                        <button type="button" class="btn btn-danger text-white"
                                            wire:click="removeTool({{ $index }})">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('tools')
                    <div class="text-danger mb-3">{{ $message }}</div>
                @enderror
                <button type="button" class="btn btn-success mb-3 text-white" wire:click="addTool">Tambah
                    Material</button>
                <hr>
                <div class="d-flex justify-content-end">
                    <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                        class="btn btn-primary">
                        <span wire:loading.remove wire:target="save">
                            Simpan Peminjaman
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script type="module">
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-alert', (data) => {
                alert(data[0].message);
            });
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('focus-kode-material', ({
                index
            }) => {
                setTimeout(() => {
                    document.getElementById('kode_material_' + index)?.focus();
                }, 50);
            });
            Livewire.on('focus-qty', ({
                index
            }) => {
                setTimeout(() => {
                    document.getElementById('qty_' + index)?.focus();
                }, 50);
            });
            Livewire.on('swal-success', (event) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: event.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });
    </script>
@endpush
</div>
