<div>
    <div class="card mb-4">
        <div class="card-header">
            Buat Surat Permintaan Material (SPM)
        </div>
        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="projectSearch"
                                    class="form-control" placeholder="Cari project asal...">
                            </div>
                            <select wire:model.live="project_id" id="project_id"
                                class="form-select @error('project_id') is-invalid @enderror">
                                <option value="">Pilih Project</option>
                                @foreach ($projects as $project)
                                    @if (empty($projectSearch) ||
                                            str_contains(strtolower($project->project_id), strtolower($projectSearch)) ||
                                            str_contains(strtolower($project->nama_project), strtolower($projectSearch)))
                                        <option value="{{ $project->project_id }}">
                                            {{ $project->project_id }}-{{ $project->nama_project }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('project_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="posisi_ct_id" class="form-label">Posisi</label>
                            <select wire:model.live="posisi_ct_id" id="posisi_ct_id"
                                class="form-select @error('posisi_ct_id') is-invalid @enderror">
                                <option value="">Pilih Posisi</option>
                                @foreach ($posisiCts as $posisi)
                                    <option value="{{ $posisi->id }}">{{ $posisi->nama_posisi_ct }}</option>
                                @endforeach
                            </select>
                            @error('posisi_ct_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col">
                        <label for="kepada" class="form-label">Kepada</label>
                        <input type="text" wire:model="kepada" id="kepada"
                            class="form-control @error('kepada') is-invalid @enderror" placeholder="Tujuan permintaan">
                        @error('kepada')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col">
                        <label for="destinasi" class="form-label">Destinasi</label>
                        <select class="form-select" wire:model.live="destinasi">
                            <option value="">-- Pilih Destinasi --</option>
                            <option value="ct">CT</option>
                            <option value="workshop">Workshop</option>
                        </select>
                        @error('destinasi')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <hr>
                @if ($destinasi !== null && $destinasi === 'workshop')
                    <strong>Pilih Tujuan Workshop</strong>
                    <br>
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
                            <select class="form-select @error('bagian_id') is-invalid @enderror" id="bagian_id"
                                wire:model.live="bagian_id" @if ($bagians->isEmpty()) disabled @endif>
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
                            <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id"
                                wire:model.defer="unit_id" @if ($units->isEmpty()) disabled @endif>
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
                @endif
                <hr>
                <h5 class="mt-4">Pilih Material</h5>

                @error('items')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Kode Material</th>
                                <th>Deskripsi</th>
                                <th>Spesifikasi</th>
                                <th>Satuan</th>
                                <th style="width: 10%;">Qty</th>
                                <th style="width: 15%;">Keterangan</th>
                                <th style="width: 15%;">Kategori</th>
                                <th style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr wire:key="item-{{ $index }}">
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm @error('items.' . $index . '.kode_material') is-invalid @enderror"
                                            wire:model.live.debounce.800ms="items.{{ $index }}.kode_material"
                                            placeholder="Kode Material">
                                        @error('items.' . $index . '.kode_material')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model="items.{{ $index }}.deskripsi" placeholder="Deskripsi"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model="items.{{ $index }}.spesifikasi"
                                            placeholder="Spesifikasi" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model="items.{{ $index }}.satuan" placeholder="satuan"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="number"
                                            class="form-control form-control-sm @error('items.' . $index . '.requested_qty') is-invalid @enderror"
                                            wire:model="items.{{ $index }}.requested_qty" placeholder="Qty">
                                        @error('items.' . $index . '.requested_qty')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model="items.{{ $index }}.keterangan"
                                            placeholder="Keterangan">
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm"
                                            wire:model="items.{{ $index }}.kategori">
                                            <option value="">Pilih</option>
                                            <option value="tool">Tool</option>
                                            <option value="apd">APD</option>
                                            <option value="consumable">Consumable</option>
                                        </select>
                                        @error('items.' . $index . '.kategori')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm text-white"
                                            wire:click="removeItem({{ $index }})">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                @if (!empty($item['error']))
                                    <tr wire:key="error-{{ $index }}">
                                        <td colspan="7">
                                            <div class="alert alert-danger mb-0 py-1">
                                                {{ $item['error'] }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (count($items) < $maxItems)
                    <button type="button" class="btn btn-primary" wire:click="addItem">
                        <i class="fas fa-plus"></i> Tambah Material
                    </button>
                @else
                    <p class="text-muted">Anda telah mencapai batas maksimum material.</p>
                @endif

                <div class="mt-4 d-flex justify-content-center gap-3">
                    <button type="button" wire:click="save" class="btn btn-success text-white">
                        <span wire:loading.remove wire:target="save">
                            Simpan
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('spm.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
