<div id="formError" class="alert alert-danger d-none"></div>

<h6 class="text-primary fw-bold mb-3">Informasi Utama</h6>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="id_cp" class="form-label">ID CP</label>
        <input type="text" class="form-control" name="id_cp" id="id_cp">
    </div>
    <div class="col-md-6 mb-3">
        <label for="no_cp" class="form-label">No CP <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="no_cp" id="no_cp" required>
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="mat" class="form-label">Material</label>
        <select class="form-select select2" name="mat" id="mat" style="width: 100%;">
            <option value=""></option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="spec_mat" class="form-label">Spec Material</label>
        <input type="text" class="form-control" name="spec_mat" id="spec_mat">
    </div>
    <div class="col-md-4 mb-3">
        <label for="mat_sisa" class="form-label">Mat Sisa</label>
        <input type="text" class="form-control" name="mat_sisa" id="mat_sisa">
    </div>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <label for="kode_proyek" class="form-label">Kode Proyek</label>
        <select class="form-select" name="kode_proyek" id="kode_proyek">
            <option value="">-- Pilih --</option>
            @foreach ($projects as $project)
                <option value="{{ $project->project_id }}">{{ $project->name_project }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label for="wbs" class="form-label">WBS</label>
        <input type="text" class="form-control" name="wbs" id="wbs">
    </div>
    <div class="col-md-3 mb-3">
        <label for="material_size" class="form-label">Material Size</label>
        <input type="text" class="form-control" name="material_size" id="material_size">
    </div>
    <div class="col-md-3 mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" name="quantity" id="quantity">
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="cutting_method" class="form-label">Cutting Method</label>
        <select class="form-select" name="cutting_method" id="cutting_method">
            <option value="">-- Pilih --</option>
            @foreach ($processes as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="sketch" class="form-label">Sket</label>
        <input type="text" class="form-control" name="sketch" id="sketch">
    </div>
    <div class="col-md-4 mb-3">
        <label for="shop" class="form-label">Shop</label>
        <input type="text" class="form-control" name="shop" id="shop">
    </div>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <label for="grup" class="form-label">Grup</label>
        <input type="text" class="form-control" name="grup" id="grup">
    </div>
    <div class="col-md-3 mb-3">
        <label for="lot" class="form-label">Lot</label>
        <input type="text" class="form-control" name="lot" id="lot">
    </div>
    <div class="col-md-3 mb-3">
        <label for="qty_lot" class="form-label">Qty Lot</label>
        <input type="number" class="form-control" name="qty_lot" id="qty_lot">
    </div>
    <div class="col-md-3 mb-3">
        <label for="diterima" class="form-label">Diterima</label>
        <input type="text" class="form-control" name="diterima" id="diterima">
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="tgl_kirim" class="form-label">Tgl Kirim</label>
        <input type="date" class="form-control" name="tgl_kirim" id="tgl_kirim">
    </div>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="text-primary fw-bold mb-0">Pieceparts</h6>
    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPiecepart">
        <i class="fas fa-plus me-1"></i> Tambah Piecepart
    </button>
</div>
<div class="table-responsive mb-3">
    <table class="table table-bordered table-sm align-middle" id="piecepartsTable">
        <thead class="text-center">
            <tr>
                <th style="width: 14%">Part No</th>
                <th style="width: 14%">Cutting Size</th>
                <th style="width: 12%">Qty / Car</th>
                <th style="width: 12%">Qty / Lot</th>
                <th style="width: 18%">Part Name</th>
                <th style="width: 22%">Remark</th>
                <th style="width: 8%">Aksi</th>
            </tr>
        </thead>
        <tbody id="piecepartsList"></tbody>
    </table>
</div>

<hr>
<h6 class="text-primary fw-bold mb-3">Instruksi & Tanggal</h6>
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="special_instruction" class="form-label">Special Instruction</label>
        <textarea class="form-control" name="special_instruction" id="special_instruction" rows="2"></textarea>
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="date_issued" class="form-label">Date Issued</label>
        <input type="date" class="form-control" name="date_issued" id="date_issued">
    </div>
    <div class="col-md-4 mb-3">
        <label for="date_revised" class="form-label">Date Revised</label>
        <input type="date" class="form-control" name="date_revised" id="date_revised">
    </div>
    <div class="col-md-4 mb-3">
        <label for="bpm_number" class="form-label">BPM Number</label>
        <input type="text" class="form-control" name="bpm_number" id="bpm_number">
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="date_of_bpm" class="form-label">Date of BPM</label>
        <input type="date" class="form-control" name="date_of_bpm" id="date_of_bpm">
    </div>
    <div class="col-md-4 mb-3">
        <label for="prepared_by" class="form-label">Prepared By</label>
        <select class="form-select" name="prepared_by" id="prepared_by">
            <option value="">-- Pilih --</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="approved_by" class="form-label">Approve By</label>
        <select class="form-select" name="approved_by" id="approved_by">
            <option value="">-- Pilih --</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="date_operator" class="form-label">Date Operator</label>
        <select class="form-select" name="date_operator" id="date_operator">
            <option value="">-- Pilih --</option>
        </select>
    </div>
</div>
