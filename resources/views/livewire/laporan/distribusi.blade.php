<div>
    <div class="card">
        <div class="card-header">
            Filter Laporan Distribusi
        </div>
        <div class="card-body">
            <form wire:submit.prevent="render">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="startDate">Tanggal Dari</label>
                            <input type="date" class="form-control" id="startDate" wire:model.live="startDate">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="endDate">Tanggal Ke</label>
                            <input type="date" class="form-control" id="endDate" wire:model.live="endDate">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kodeMaterial">Kode Material</label>
                            <input type="text" class="form-control" id="kodeMaterial"
                                wire:model.live.debounce.300ms="kodeMaterial" placeholder="Cari Kode Material">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="projectId">Project</label>
                            <select class="form-control" id="projectId" wire:model.live="projectId">
                                <option value="">Semua Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="departemenId">Departemen</label>
                            <select class="form-control" id="departemenId" wire:model.live="departemenId">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bagianId">Bagian</label>
                            <select class="form-control" id="bagianId" wire:model.live="bagianId"
                                {{ !$departemenId ? 'disabled' : '' }}>
                                <option value="">Semua Bagian</option>
                                @foreach ($bagians as $bagian)
                                    <option value="{{ $bagian->id }}">{{ $bagian->nama_bagian }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="unitId">Unit</label>
                            <select class="form-control" id="unitId" wire:model.live="unitId"
                                {{ !$bagianId ? 'disabled' : '' }}>
                                <option value="">Semua Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <select class="form-control" id="kategori" wire:model.live="kategori">
                                <option value="">Semua Kategori</option>
                                <option value="tool">Tool</option>
                                <option value="consumable">Consumable</option>
                                <option value="apd">Apd</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="tipeDistribusi">Tipe Distribusi</label>
                            <select class="form-control" id="tipeDistribusi" wire:model.live="tipeDistribusi">
                                <option value="">Semua Tipe</option>
                                <option value="penyerahan">Penyerahan</option>
                                <option value="peminjaman">Peminjaman</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-start">
                        <button type="button" class="btn btn-danger text-white"
                            wire:click="resetFilters">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            Hasil Laporan
            @if (!$projectId)
                <span class="text-danger">* Harap Pilih Project Terlebih Dahulu</span>
                <button class="btn btn-success text-white" wire:click="exportToExcel">Export Semua Project</button>
            @else
                <button class="btn btn-success text-white" wire:click="exportToExcel">Export</button>
            @endif
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Material</th>
                        <th>Deskripsi</th>
                        <th>Spesifikasi</th>
                        <th>Jenis Material</th>
                        <th>Posisi</th>
                        <th>Project</th>
                        <th>Total Distribusi</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($results as $result)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $result->kode_material }}</td>
                            <td>{{ $result->deskripsi }}</td>
                            <td>{{ $result->spesifikasi }}</td>
                            <td class="text-center">{{ strtoupper($result->jenis_material) }}</td>
                            <td class="text-center">{{ $result->posisiCT->nama_posisi_ct }}</td>
                            <td class="text-center">{{ $result->nama_project }}</td>
                            <td class="text-center">
                                {{ rtrim(rtrim(number_format($result->total_distribusi, 4, '.', ''), '0'), '.') }}
                            </td>
                            <td class="text-center">{{ $result->satuan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
