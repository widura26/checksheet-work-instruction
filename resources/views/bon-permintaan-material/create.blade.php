@extends('layouts.app')

@section('title', 'Tambah Bon Permintaan Material')

@push('css')
    <style>
        .icon {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Tambah Bon Permintaan Material</h5>
            <a href="{{ route('cutting-plan.index') }}" class="btn btn-secondary">Kembali ke Cutting Plan</a>
        </div>
        <div class="card-body">
            <form id="bpmForm" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="cutting_plan_id" class="form-label">Cutting Plan</label>
                        <select class="form-select" id="cutting_plan_id" name="cutting_plan_id">
                            @if (!$selectedId)
                                <option value="">-- Pilih Cutting Plan --</option>
                            @endif
                            @foreach ($cuttingPlans as $cp)
                                <option value="{{ $cp->id }}" {{ $selectedId == $cp->id ? 'selected' : '' }}>
                                    {{ $cp->no_cp }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="material_id" class="form-label">Material</label>
                        <select class="form-select" id="material_id" name="material_id">
                            <option value="">-- Pilih Material --</option>
                            @foreach ($materials as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->kode_material }} - {{ $m->deskripsi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="bpm_no" class="form-label">No BPM</label>
                        <input type="text" class="form-control" id="bpm_no" name="bpm_no">
                    </div>

                    <div class="col-md-6">
                        <label for="total" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="total" name="total">
                    </div>

                    <div class="col-md-6">
                        <label for="unit" class="form-label">Unit</label>
                        <input type="text" class="form-control" id="unit" name="unit">
                    </div>

                    <div class="col-md-6">
                        <label for="request_date" class="form-label">Tanggal Permintaan</label>
                        <input type="date" class="form-control" id="request_date" name="request_date">
                    </div>

                    <div class="col-md-6">
                        <label for="bprm_no" class="form-label">Nomor BPRM</label>
                        <input type="text" class="form-control" id="bprm_no" name="bprm_no">
                    </div>

                    <div class="col-md-6">
                        <label for="brpm_date" class="form-label">Tanggal BPRM</label>
                        <input type="date" class="form-control" id="brpm_date" name="brpm_date">
                    </div>

                    <div class="col-md-6">
                        <label for="bprm_total" class="form-label">Jumlah BPRM</label>
                        <input type="number" class="form-control" id="bprm_total" name="bprm_total">
                    </div>

                    <div class="col-md-6">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="text" class="form-control" id="stock" name="stock">
                    </div>

                    <div class="col-12">
                        <label for="specification" class="form-label">Keterangan / Spesification</label>
                        <textarea class="form-control" id="specification" name="specification" rows="3"></textarea>
                    </div>
                </div>

                <div id="bpmError" class="alert alert-danger d-none mt-3"></div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.BPM = {
            routes: {
                store: "{{ route('bon-permintaan-material.store') }}"
            },
            cpMaterials: @json($cpMaterials)
        };
    </script>
    <script type="module" src="{{ asset('js/bon-permintaan-material/create.js') }}"></script>
@endpush
