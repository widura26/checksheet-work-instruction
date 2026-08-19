@extends('layouts.app')

@section('title', 'Edit Material')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Material</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Terdapat beberapa masalah dengan inputan Anda.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('tool.update', $material->public_id) }}" id="myForm">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode_material" class="form-label">Kode Material :</label>
                            <br>
                            <strong>{{ $material->kode_material }}</strong>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi :</label>
                            <input type="hidden" name="project_id" value={{ $material->stockProject[0]->project_id }}>
                            <input type="hidden" name="posisi_ct_id" value={{ $material->stockProject[0]->pivot->posisi_ct_id }}>
                            <br>
                            <strong> {{ $material->deskripsi }} </strong>
                        </div>
                        <div class="mb-3">
                            <label for="spesifikasi" class="form-label">Spesifikasi :</label>
                            <br>
                            <strong>
                                {{ $material->spesifikasi ? $material->spesifikasi : '-' }}
                            </strong>
                        </div>
                        <div class="mb-3">
                            <label for="total_stok" class="form-label">Total Stok Di Project</label>
                            <input class="form-control @error('total_stok') is-invalid @enderror" type="text"
                                id="total_stok" name="total_stok"
                                value="{{ old('total_stok', rtrim(rtrim(number_format($material->stockProject[0]->pivot->qty, 4, '.', ''), '0'), '.')) }}"></input>
                            @error('total_stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="jenis_material" class="form-label">Jenis Material</label>
                            <select name="jenis_material" class="form-select @error('jenis_material') is-invalid @enderror"
                                id="">
                                <option value="">Pilih Jenis Material</option>
                                <option value="tool"
                                    {{ old('jenis_material', $material->jenis_material) == 'tool' ? 'selected' : '' }}>Tool
                                </option>
                                <option value="apd"
                                    {{ old('jenis_material', $material->jenis_material) == 'apd' ? 'selected' : '' }}>APD
                                </option>
                                <option value="consumable"
                                    {{ old('jenis_material', $material->jenis_material) == 'consumable' ? 'selected' : '' }}>
                                    Consumable</option>
                            </select>
                            @error('jenis_material')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <br>
                            <strong>
                                {{ $material->satuan }}
                            </strong>
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Posisi</label>
                            <br>
                            <strong>
                                {{ $material->posisiCT->nama_posisi_ct }}
                            </strong>
                        </div>
                        <div class="mb-3">
                            <label for="vendor" class="form-label">Vendor</label>
                            <select class="form-select @error('vendor') is-invalid @enderror" id="vendor" name="vendor">
                                <option value="">Pilih Vendor</option>
                                <option value="vendor"
                                    {{ old('vendor', $material->vendor) == 'vendor' ? 'selected' : '' }}>
                                    Vendor</option>
                                <option value="INKA" {{ old('vendor', $material->vendor) == 'INKA' ? 'selected' : '' }}>
                                    INKA
                                </option>
                            </select>
                            @error('vendor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_vendor" class="form-label">Keterangan Vendor</label>
                            <textarea class="form-control @error('keterangan_vendor') is-invalid @enderror" id="keterangan_vendor"
                                name="keterangan_vendor" rows="3">{{ old('keterangan_vendor', $material->keterangan_vendor) }}</textarea>
                            @error('keterangan_vendor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </div>
                    <div class="col-md-6">
                        <a onclick="history.back()" class="btn btn-outline-secondary w-100">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
