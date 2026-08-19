<div>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Buat Transfer Posting Baru</h1>
            <a href="{{ route('transfer-posting.index') }}" class="btn btn-secondary shadow-sm" wire:navigate>
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>

        @if ($alertMessage)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $alertMessage }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Transfer</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="from_project_id">From</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" wire:model.live.debounce.300ms="from_project_search"
                                        class="form-control" placeholder="Cari project asal...">
                                </div>
                                <select wire:model.live="from_project_id" id="from_project_id" class="form-control"
                                    required>
                                    <option value="">Pilih Project Asal</option>
                                    @foreach ($projects as $project)
                                        @if (empty($from_project_search) ||
                                                str_contains(strtolower($project->project_id), strtolower($from_project_search)) ||
                                                str_contains(strtolower($project->nama_project), strtolower($from_project_search)))
                                            <option value="{{ $project->project_id }}">
                                                {{ $project->project_id }}-{{ $project->nama_project }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('from_project_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_project_id">Dest</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" wire:model.live.debounce.300ms="to_project_search"
                                        class="form-control" placeholder="Cari project tujuan...">
                                </div>
                                <select wire:model.live="to_project_id" id="to_project_id" class="form-control"
                                    required>
                                    <option value="">Pilih Project Tujuan</option>
                                    @foreach ($projectsDest as $project)
                                        @if (empty($to_project_search) ||
                                                str_contains(strtolower($project->project_id), strtolower($to_project_search)) ||
                                                str_contains(strtolower($project->nama_project), strtolower($to_project_search)))
                                            <option value="{{ $project->project_id }}">
                                                {{ $project->project_id }}-{{ $project->nama_project }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('to_project_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="from_posisi_ct_id">Posisi CT Asal</label>
                                <select wire:model.live="from_posisi_ct_id" id="from_posisi_ct_id" class="form-control"
                                    required>
                                    <option value="">Pilih Posisi CT</option>
                                    @foreach ($posisiCts as $posisiCt)
                                        <option value="{{ $posisiCt->id }}">
                                            {{ $posisiCt->nama_posisi_ct }}</option>
                                    @endforeach
                                </select>
                                @error('from_posisi_ct_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_posisi_ct_id">Posisi CT Tujuan</label>
                                <select wire:model.live="to_posisi_ct_id" id="to_posisi_ct_id" class="form-control"
                                    required>
                                    <option value="">Pilih Posisi CT Tujuan</option>
                                    @foreach ($posisiCtsDest as $posisiCt)
                                        <option value="{{ $posisiCt->id }}">
                                            {{ $posisiCt->nama_posisi_ct }}</option>
                                    @endforeach
                                </select>
                                @error('to_posisi_ct_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transfer_date">Tanggal Transfer</label>
                                <input wire:model="transfer_date" type="date" id="transfer_date" class="form-control"
                                    required>
                                @error('transfer_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Item</h6>
                    <button type="button" wire:click="addItem" class="btn btn-success btn-sm text-white"
                        @if (!$availableTools) disabled @endif>
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="items-table">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Posisi</th>
                                    <th>Stok Tersedia</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $index => $item)
                                    <tr wire:key="item-{{ $index }}">
                                        <td>
                                            <div class="row g-1">
                                                <div class="col-4">
                                                    <input type="text"
                                                        wire:model.live.debounce.300ms="items.{{ $index }}.search"
                                                        class="form-control form-control-sm" placeholder="Cari...">
                                                </div>
                                                <div class="col-8">
                                                    <select wire:model.live="items.{{ $index }}.kode_material"
                                                        class="form-select form-select-sm">
                                                        <option value="">Pilih Material</option>
                                                        @foreach ($availableTools as $stockItem)
                                                            @if (empty($items[$index]['search']) ||
                                                                    str_contains(strtolower($stockItem->tool->kode_material), strtolower($items[$index]['search'])) ||
                                                                    str_contains(strtolower($stockItem->tool->deskripsi), strtolower($items[$index]['search'])))
                                                                <option
                                                                    value="{{ $stockItem->tool->kode_material }}|{{ $stockItem->posisi_ct_id }}">
                                                                    ({{ $stockItem->tool->kode_material }})
                                                                    -
                                                                    {{ $stockItem->posisiCt->nama_posisi_ct }}-
                                                                    {{ $stockItem->tool->deskripsi }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @error('items.' . $index . '.kode_material')
                                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                            @enderror
                                        </td>
                                        <td style="min-width: 150px;">
                                            <input type="text" wire:model="items.{{ $index }}.posisi_name"
                                                class="form-control form-control-sm" readonly>
                                        </td>
                                        <td style="width: 100px;">
                                            <input type="text" wire:model="items.{{ $index }}.stock"
                                                class="form-control form-control-sm text-center" readonly>
                                        </td>
                                        <td style="width: 100px;">
                                            <input type="number" wire:model="items.{{ $index }}.qty"
                                                class="form-control form-control-sm text-center" min="1">
                                            @error('items.' . $index . '.qty')
                                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                            @enderror
                                        </td>
                                        <td style="width: 80px;" class="text-center">
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                class="btn btn-danger btn-sm text-white">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @error('items')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" wire:click="save" class="btn btn-primary">
                    <span wire:loading.remove wire:target="save">Simpan Transfer</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('js')
    <script type="module">
        // Listener for show-alert event
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-alert', (message, type) => {
                if (type === 'error') {
                    alert('Error: ' + message);
                } else if (type === 'warning') {
                    alert('Warning: ' + message);
                } else {
                    alert(message);
                }
            });
        });
    </script>
@endpush
