<div id="formError" class="alert alert-danger d-none"></div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="no_konv" class="form-label">No Konv</label>
        <input type="text" class="form-control" name="no_konv" id="no_konv" required
            value="{{ old('no_konv', $barcode->no_konv ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="part_no" class="form-label">Part No</label>
        <input type="text" class="form-control" name="part_no" id="part_no"
            value="{{ old('part_no', $barcode->part_no ?? '') }}">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="partno1" class="form-label">Part No1</label>
        <input type="text" class="form-control" name="partno1" id="partno1"
            value="{{ old('partno1', $barcode->partno1 ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="wbs" class="form-label">WBS</label>
        <input type="text" class="form-control" name="wbs" id="wbs"
            value="{{ old('wbs', $barcode->wbs ?? '') }}">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="lot" class="form-label">Lot</label>
        <input type="text" class="form-control" name="lot" id="lot"
            value="{{ old('lot', $barcode->lot ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="PR" class="form-label">PR</label>
        <input type="text" class="form-control" name="PR" id="PR"
            value="{{ old('PR', $barcode->PR ?? '') }}">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="oka_id" class="form-label">Oka</label>
        <select class="form-select" name="oka_id" id="oka_id">
            <option value="">-- Pilih Oka --</option>
            @foreach ($okas as $o)
                <option value="{{ $o->id }}" {{ old('oka_id', $barcode->oka_id ?? '') == $o->id ? 'selected' : '' }}>
                    {{ $o->oka }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="wi_trn_id" class="form-label">Work Instruction</label>
        <select class="form-select" name="wi_trn_id" id="wi_trn_id">
            <option value="">-- Pilih Work Instruction --</option>
            @foreach ($wiTrns as $w)
                <option value="{{ $w->id }}" {{ old('wi_trn_id', $barcode->wi_trn_id ?? '') == $w->id ? 'selected' : '' }}>
                    {{ $w->no_exp }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Status</label>
        <input type="text" class="form-control" name="status" id="status"
            value="{{ old('status', $barcode->status ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="bil" class="form-label">Bil</label>
        <input type="text" class="form-control" name="bil" id="bil"
            value="{{ old('bil', $barcode->bil ?? '') }}">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="tgl" class="form-label">Tanggal</label>
        <input type="date" class="form-control" name="tgl" id="tgl"
            value="{{ old('tgl', isset($barcode->tgl) ? \Carbon\Carbon::parse($barcode->tgl)->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="tgl_print" class="form-label">Tanggal Print</label>
        <input type="date" class="form-control" name="tgl_print" id="tgl_print"
            value="{{ old('tgl_print', isset($barcode->tgl_print) ? \Carbon\Carbon::parse($barcode->tgl_print)->format('Y-m-d') : '') }}">
    </div>
</div>
