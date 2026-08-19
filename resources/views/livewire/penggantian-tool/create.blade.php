<div>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Pilih Departemen, Bagian, dan Unit</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="departemen">Departemen</label>
                        <select wire:model.live="departemenId" id="departemen" class="form-control">
                            <option value="">Pilih Departemen</option>
                            @foreach ($departemens as $departemen)
                                <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bagian">Bagian</label>
                        <select wire:model.live="bagianId" id="bagian" class="form-control"
                            @if (empty($bagians)) disabled @endif>
                            <option value="">Pilih Bagian</option>
                            @foreach ($bagians as $bagian)
                                <option value="{{ $bagian->id }}">{{ $bagian->nama_bagian }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <select wire:model.live="unitId" id="unit" class="form-control"
                            @if (empty($units)) disabled @endif>
                            <option value="">Pilih Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Tool</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
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
                <table class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Total Penyerahan</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    @if ($departemenId && $bagianId && $unitId)
                        <tbody>
                            @forelse ($distribusi as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->kode_material }}</td>
                                    <td>{{ $item->deskripsi }}</td>
                                    <td class="text-center">
                                        {{ rtrim(rtrim(number_format($item->total_distribusi, 4, '.', ''), '0'), '.') }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm text-white"
                                            wire:click="penggantian({{ $item->tool_id }},{{ $item->total_distribusi }})">Penggantian</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    @endif
                </table>
                @if ($showModal)
                    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);"
                        tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Input Jumlah Yang Akan Diganti</h5>
                                    <button type="button" class="btn-close" data-coreui-dismiss="modal"
                                        aria-label="Close" wire:click="closeModal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="jumlahDikurangi">Jumlah Kerusakan:</label>
                                        <input type="number" class="form-control" id="jumlahDikurangi"
                                            wire:model.defer="qtyKerusakan">
                                        @error('qtyKerusakan')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2 mb-2">
                                        <label for="keterangan">Keterangan : (Opsional)</label>
                                        <input type="text" class="form-control" id="keterangan"
                                            wire:model.defer="keterangan">
                                    </div>
                                    <div class="form-group">
                                        <label for="buktiUpload">Upload bukti:</label>
                                        <br>
                                        <input type="file" class="form-control" id="buktiUpload"
                                            wire:model="fileBukti">
                                        @error('fileBukti')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal"
                                        wire:click="closeModal">Close</button>
                                    <button wire:click="save" type="button" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
