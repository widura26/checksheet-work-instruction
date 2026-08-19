<div>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Penyerahan</h5>
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
                        <label for="nip_peminjam" class="form-label">NIP Penerima ( Tekan Enter untuk mencari data
                            )</label>
                        <input type="text" class="form-control @error('nip_peminjam') is-invalid @enderror"
                            id="nip_peminjam" wire:keydown.enter.prevent="searchNipData" wire:model.live="nip_peminjam"
                            placeholder="NIP Penerima">
                        @error('nip_peminjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nama_peminjam" class="form-label">Nama Penerima</label>
                        <input type="text" id="nama_peminjam"
                            class="form-control @error('nama_peminjam') is-invalid @enderror"
                            wire:model.defer="nama_peminjam" placeholder="Nama Penerima">
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
                <div class="row">
                    <div class="col-md-6">
                        <label for="project_id" class="form-label">Pilih Project :</label>
                        <select class="form-select @error('project_id') is-invalid @enderror" id="project_id"
                            wire:model.live="project_id">
                            <option value="">Pilih Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" id="keterangan"
                            class="form-control @error('keterangan') is-invalid @enderror" wire:model.defer="keterangan"
                            placeholder="Opsional">
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if (Auth::user()->hasRole(['admin']))
                            <label for="" class="my-3">Tanggal Penyerahan (Khusus Admin)</label>
                            <input type="date" class="form-control" wire:model.defer="backDateAdmin">
                        @endif
                    </div>
                </div>

                @php
                    $categoryConfigs = [
                        'consumable' => ['label' => 'Consumable', 'class' => 'success'],
                        'apd' => ['label' => 'APD', 'class' => 'warning'],
                        'tool' => ['label' => 'Tool', 'class' => 'dark'],
                    ];
                @endphp

                @foreach ($categoryConfigs as $catKey => $config)
                    <hr class="mt-5">
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="mb-0 me-2 text-{{ $config['class'] }}">Daftar {{ $config['label'] }}</h5>
                        <small class="text-muted">( Masukkan kode material dan tekan Enter )</small>
                    </div>
                    <div class="table-responsive mb-3">
                        @php $itemsProp = $catKey . 'Items'; @endphp
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 250px">Kode Material</th>
                                    <th>Deskripsi</th>
                                    <th>Spesifikasi</th>
                                    <th style="width: 100px">Stok Tersedia</th>
                                    <th class="text-center" style="width: 120px">Jumlah</th>
                                    <th style="width: 100px">Satuan</th>
                                    <th style="width: 80px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->{$itemsProp} as $index => $item)
                                    <tr wire:key="row-{{ $catKey }}-{{ $index }}">
                                        <td>
                                            <input type="text"
                                                wire:model="{{ $itemsProp }}.{{ $index }}.kode_material"
                                                wire:keydown.enter.prevent="onKodeMaterialChanged({{ $index }}, '{{ $catKey }}')"
                                                class="form-control @error($itemsProp . '.' . $index . '.kode_material') is-invalid @enderror"
                                                id="kode_material_{{ $catKey }}_{{ $index }}">
                                            @error($itemsProp . '.' . $index . '.kode_material')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if (isset($item['konversi_detail']))
                                                <small class="text-success d-block">
                                                    1 {{ $item['konversi_detail']['satuan_distribusi'] }}
                                                    = {{ $item['konversi_detail']['nilai_konversi'] }}
                                                    {{ $item['konversi_detail']['satuan_sap'] }}
                                                </small>
                                            @endif
                                        </td>
                                        <td><input type="text" class="form-control bg-light"
                                                value="{{ $item['deskripsi'] }}" readonly></td>
                                        <td><input type="text" class="form-control bg-light"
                                                value="{{ $item['spesifikasi'] }}" readonly></td>
                                        <td><input type="text" class="form-control bg-light text-center"
                                                value="{{ $item['stok_tersedia'] }}" readonly></td>
                                        <td>
                                            <input type="number" id="qty_{{ $catKey }}_{{ $index }}"
                                                wire:model.defer="{{ $itemsProp }}.{{ $index }}.qty"
                                                wire:keydown.enter.prevent="addTool('{{ $catKey }}')"
                                                class="form-control @error($itemsProp . '.' . $index . '.qty') is-invalid @enderror"
                                                min="1" step="1">
                                            @error($itemsProp . '.' . $index . '.qty')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if (isset($item['konversi_detail']))
                                                <small class="text-success d-block">
                                                    Masukkan Jumlah dalam
                                                    {{ $item['konversi_detail']['satuan_distribusi'] }}
                                                </small>
                                            @endif
                                        </td>
                                        <td><input type="text" class="form-control bg-light"
                                                value="{{ $item['satuan'] }}" readonly></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger text-white"
                                                wire:click="removeTool({{ $index }}, '{{ $catKey }}')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-outline-{{ $config['class'] }} btn-sm"
                            wire:click="addTool('{{ $catKey }}')">
                            + Tambah {{ $config['label'] }}
                        </button>
                    </div>
                @endforeach

                <hr class="mt-5">
                <div class="d-flex justify-content-end">
                    @if ($errors->any())
                        <div class="alert alert-danger me-4">
                            <strong>Terjadi kesalahan!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                        class="btn btn-primary">
                        <span wire:loading.remove wire:target="save">
                            Simpan Penyerahan
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
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-alert', (data) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data[0].message,
                    timer: 5000,
                    showConfirmButton: true
                });
            });
            Livewire.on('focus-kode-material', ({
                index,
                category
            }) => {
                setTimeout(() => {
                    document.getElementById('kode_material_' + category + '_' + index)?.focus();
                }, 100);
            });

            Livewire.on('focus-qty', ({
                index,
                category
            }) => {
                setTimeout(() => {
                    document.getElementById('qty_' + category + '_' + index)?.focus();
                }, 100);
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
</form>
</div>
</div>
</div>
</div>
