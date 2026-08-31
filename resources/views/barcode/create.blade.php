@extends('layouts.app')

@section('title', 'Tambah Barcode')

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
            <h5 class="card-title mb-0">Tambah Barcode per Part</h5>
            <a href="{{ route('cutting-plan.index') }}" class="btn btn-secondary">Kembali ke Cutting Plan</a>
        </div>
        <div class="card-body">
            <div class="mb-3" style="max-width: 480px;">
                <label for="cutting_plan_id" class="form-label">Cutting Plan</label>
                <select class="form-select" id="cutting_plan_id">
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

            <div id="partsWrap" class="d-none">
                <h6 class="mb-3">Daftar Part</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="parts-table">
                        <thead class="text-center">
                            <tr>
                                <th>Part No</th>
                                <th>Cutting Size</th>
                                <th>Part Name</th>
                                <th>Barcode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="partsList"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Barcode -->
    <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="barcodeForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="barcode_method" value="POST">
                    <input type="hidden" name="cutting_plan_id" id="f_cutting_plan_id">
                    <input type="hidden" name="part_id" id="f_part_id">
                    <input type="hidden" id="f_barcode_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="barcodeModalLabel">Tambah Barcode</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="barcodeError" class="alert alert-danger d-none"></div>
                        <div class="mb-2">
                            <label class="form-label">Part</label>
                            <input type="text" class="form-control" id="f_part_label" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="f_wbs" class="form-label">WBS</label>
                            <input type="text" class="form-control" name="wbs" id="f_wbs">
                        </div>
                        <div class="mb-3">
                            <label for="f_lot" class="form-label">Lot</label>
                            <input type="text" class="form-control" name="lot" id="f_lot">
                        </div>
                        <div class="mb-3">
                            <label for="f_part_no" class="form-label">No Part</label>
                            <input type="text" class="form-control" name="part_no" id="f_part_no" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnBarcodeSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.BC = {
            routes: {
                parts: "{{ route('barcode.parts') }}",
                store: "{{ route('barcode.store') }}",
                update: "{{ route('barcode.update', '__ID__') }}",
                destroy: "{{ route('barcode.destroy', '__ID__') }}"
            }
        };
    </script>
    <script type="module" src="{{ asset('js/barcode/create.js') }}"></script>
@endpush
