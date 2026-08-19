<div>
    <form wire:submit.prevent="save" id="material-create-form" autocomplete="off" enctype="multipart/form-data">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Formulir Update Stok Material</h5>
                <div wire:loading wire:target="save" class="text-muted">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    Menyimpan...
                </div>
            </div>
            <div class="card-body">
                {{-- Global Error/Success Messages --}}
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Perhatian!</strong> Terdapat beberapa masalah dengan inputan Anda.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Static Form Section --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="project_id" class="form-label">Pilih Project <span
                                class="text-danger">*</span></label>
                        @if ($projects->count())
                            <div wire:ignore>
                                <select class="form-select @error('projectId') is-invalid @enderror"
                                    id="project_id_livewire" wire:model.live="projectId">
                                    <option value="">Pilih Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->project_id }}">
                                            {{ $project->project_id }} - {{ $project->nama_project }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('projectId')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        @else
                            <div class="alert alert-warning">Tidak ada data project.</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="posisi_ct_id" class="form-label">Posisi CT <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('posisiCtId') is-invalid @enderror" id="posisi_ct_id"
                            wire:model.live="posisiCtId">
                            <option value="">Pilih Posisi</option>
                            @foreach ($posisiCts as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->nama_posisi_ct }}</option>
                            @endforeach
                        </select>
                        @error('posisiCtId')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="dokumen_pengambilan" class="form-label">Dokumen Pengambilan <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('dokumen_pengambilan') is-invalid @enderror"
                            id="dokumen_pengambilan" wire:model="dokumen_pengambilan">
                            <option value="">Pilih Dokumen</option>
                            <option value="bpm">BPM</option>
                            <option value="memo">MEMO</option>
                            <option value="surat_jalan">Surat Jalan</option>
                        </select>
                        @error('dokumen_pengambilan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nomor_dokumen" class="form-label">Nomor Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nomor_dokumen') is-invalid @enderror"
                            id="nomor_dokumen" wire:model="nomor_dokumen" placeholder="Masukkan nomor dokumen">
                        @error('nomor_dokumen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="dokumen_pengambilan_file" class="form-label">Upload Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="file"
                            class="form-control @error('dokumen_pengambilan_file') is-invalid @enderror"
                            id="dokumen_pengambilan_file" wire:model="dokumen_pengambilan_file"
                            accept=".pdf,.jpg,.jpeg,.png">
                        @error('dokumen_pengambilan_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div wire:loading wire:target="dokumen_pengambilan_file" class="mt-1 text-muted">Uploading...
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Dynamic Items Section --}}
                <h5 class="mb-3">Detail Material</h5>

                @foreach ($items as $index => $item)
                    <div class="card mb-3" wire:key="item-{{ $index }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-start gap-2">
                                @if ($item['mode'] == 'create_new_material')
                                    <span class="badge bg-primary">Input Material</span>
                                @endif
                                @if ($item['mode'] == 'update_stock')
                                    <span class="badge bg-success">Tambah Stock</span>
                                @endif
                                @if ($item['mode'] == 'create_stock_for_existing_material')
                                    <span class="badge bg-success">Tambah Stock</span>
                                @endif
                                <div wire:loading wire:target="onKodeMaterialChanged" class="text-muted">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Mencari...
                                </div>
                            </div>
                            {{-- Remove button --}}
                            <div class="text-end">
                                <button type="button" class="btn-close"
                                    wire:click.prevent="removeItem({{ $index }})" aria-label="Close"></button>
                            </div>

                            <div class="row g-3">
                                {{-- Kode Material --}}
                                <div class="col-md-4">
                                    <label for="kode_material_{{ $index }}" class="form-label">Kode
                                        Material.
                                    </label>
                                    <input type="text"
                                        class="form-control @error('items.' . $index . '.kode_material') is-invalid @enderror"
                                        placeholder="Kode Material"
                                        wire:model="items.{{ $index }}.kode_material"
                                        id="kode_material_{{ $index }}"
                                        wire:keydown.enter.prevent="onKodeMaterialChanged({{ $index }})">
                                    <small class="text-danger fst-italic mt-1">
                                        Tekan Enter Setelah Mengisi Kode Material Untuk Melakukan Validasi Kode Material
                                    </small>
                                </div>
                                {{-- Jumlah --}}
                                <div class="col-md-2">
                                    <label for="qty_{{ $index }}" class="form-label">Jumlah</label>
                                    <input type="number"
                                        class="form-control @error('items.' . $index . '.qty') is-invalid @enderror"
                                        placeholder="Jumlah" wire:model="items.{{ $index }}.qty"
                                        min="1" id="qty_{{ $index }}"
                                        wire:keydown.enter.prevent="addItem">
                                </div>
                                {{-- Jenis Material --}}
                                <div class="col-md-3">
                                    <label for="jenis-material-{{ $index }}" class="form-label">Jenis
                                        Material</label>
                                    <select
                                        class="form-select @error('items.' . $index . '.jenis_material') is-invalid @enderror"
                                        id="jenis-material-{{ $index }}"
                                        wire:model="items.{{ $index }}.jenis_material"
                                        {{ $item['mode'] !== 'create_new_material' ? 'disabled' : '' }}>
                                        <option value="">Pilih Jenis</option>
                                        <option value="tool">Tool</option>
                                        <option value="apd">APD</option>
                                        <option value="consumable">Consumable</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Stok Proyek Saat Ini</label>
                                    <input type="text" class="form-control" value="{{ $item['currentStock'] }}"
                                        readonly disabled>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                {{-- Deskripsi --}}
                                <div class="col-md-6">
                                    <label class="form-label">Deskripsi</label>
                                    <input type="text" class="form-control" value="{{ $item['deskripsi'] }}"
                                        readonly disabled>
                                </div>
                                {{-- Spesifikasi --}}
                                <div class="col-md-5">
                                    <label class="form-label">Spesifikasi</label>
                                    <input type="text" class="form-control" value="{{ $item['spesifikasi'] }}"
                                        readonly disabled>
                                </div>
                                {{-- Satuan --}}
                                <div class="col-md-1">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" class="form-control" value="{{ $item['satuan'] }}"
                                        readonly disabled>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                {{-- Vendor --}}
                                <div class="col-md-4">
                                    <label for="vendor-{{ $index }}" class="form-label">Barang Datang Dari
                                    </label>
                                    <select
                                        class="form-select @error('items.' . $index . '.vendor') is-invalid @enderror"
                                        id="vendor-{{ $index }}"
                                        wire:model="items.{{ $index }}.vendor">
                                        <option value="">Pilih</option>
                                        <option value="vendor" {{ $item['vendor'] === 'vendor' ? 'selected' : '' }}>
                                            Vendor</option>
                                        <option value="INKA" {{ $item['vendor'] === 'INKA' ? 'selected' : '' }}>INKA
                                        </option>
                                    </select>
                                </div>
                                {{-- Keterangan Vendor --}}
                                <div class="col-md-8">
                                    <label for="keterangan-vendor-{{ $index }}"
                                        class="form-label">Keterangan</label>
                                    <input type="text"
                                        class="form-control @error('items.' . $index . '.keterangan_vendor') is-invalid @enderror"
                                        id="keterangan-vendor-{{ $index }}"
                                        placeholder="(Opsional) Keterangan"
                                        wire:model="items.{{ $index }}.keterangan_vendor">
                                </div>
                            </div>

                            @if ($item['error'])
                                <div class="mt-2 text-danger">
                                    <small>{{ $item['error'] }}</small>
                                </div>
                            @endif
                            @error('items.' . $index . '.*')
                                <div class="mt-2 text-danger">
                                    <small>{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                    </div>
                @endforeach


                <div class="mt-3">
                    <button type="button" class="btn btn-primary" wire:click.prevent="addItem">
                        <i class="fas fa-plus"></i> Tambah Material
                    </button>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('tool.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="fas fa-save"></i> Simpan
                    </span>
                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </form>

    @push('js')
        <script type="module">
            document.addEventListener('livewire:init', () => {
                Livewire.on('focus-kode-material', ({
                    index
                }) => {
                    setTimeout(() => {
                        const nextInput = document.getElementById('kode_material_' + index);
                        if (nextInput) {
                            nextInput.focus();
                            nextInput.scrollIntoView({
                                block: 'center',
                                behavior: 'smooth'
                            });
                        }
                    }, 50);
                });

                Livewire.on('focus-qty', ({
                    index
                }) => {
                    setTimeout(() => {
                        const qtyInput = document.getElementById('qty_' + index);
                        if (qtyInput) {
                            qtyInput.focus();
                        }
                    }, 50);
                });

                let projectIdSelect = $('#project_id_livewire');
                if (projectIdSelect.length) {
                    projectIdSelect.select2({
                        placeholder: 'Pilih Project',
                        width: '100%',
                    }).on('change', function(e) {
                        @this.set('projectId', e.target.value, false);
                    });
                }

                Livewire.hook('message.processed', (message, component) => {
                    if (component.name === 'material.materials-create') {
                        let livewireValue = @this.get('projectId');
                        if (projectIdSelect.val() !== livewireValue) {
                            projectIdSelect.val(livewireValue).trigger('change.select2');
                        }
                    }
                });
            });
        </script>
    @endpush
</div>
