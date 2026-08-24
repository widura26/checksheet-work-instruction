<style>
    .cp-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem 1.25rem;
        align-items: start;
    }

    .cp-form-grid .grid-full {
        grid-column: 1 / -1;
    }

    .cp-form-grid .form-label {
        margin-bottom: .35rem;
    }

    /* Samakan tampilan Select2 (Material) dengan .form-select Bootstrap */
    .cp-form-grid .select2-container {
        width: 100% !important;
    }

    .cp-form-grid .select2-container--default .select2-selection--single {
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
        font-size: .875rem;
        line-height: 1.5;
        border: 1px solid #d8dbe0;
        border-radius: .375rem;
        display: flex;
        align-items: center;
    }

    .cp-form-grid .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        padding-right: 1.5rem;
        line-height: 1.5;
        color: #4b4b4b;
    }

    .cp-form-grid .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: .625rem;
    }

    .cp-form-grid .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #879197 transparent transparent transparent;
    }

    .cp-form-grid .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #321fdb;
        box-shadow: 0 0 0 .2rem rgba(50, 31, 219, .25);
    }

    @media (max-width: 575.98px) {
        .cp-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="formError" class="alert alert-danger d-none"></div>

<div class="cp-form-grid">
    <h6 class="text-primary fw-bold mb-1 grid-full">Informasi Utama</h6>

    <div>
        <label for="no_cp" class="form-label">No CP <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="no_cp" id="no_cp" required>
    </div>
    <div>
        <label for="mat" class="form-label">Material</label>
        <select class="form-select select2" name="mat" id="mat" style="width: 100%;">
            <option value=""></option>
        </select>
    </div>
    <div>
        <label for="kode_proyek" class="form-label">Kode Proyek</label>
        <select class="form-select" name="kode_proyek" id="kode_proyek">
            <option value="">-- Pilih --</option>
            @foreach ($projects as $project)
                <option value="{{ $project->project_id }}">{{ $project->name_project }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="wbs" class="form-label">WBS</label>
        <input type="text" class="form-control" name="wbs" id="wbs">
    </div>
    <div>
        <label for="material_size" class="form-label">Material Size</label>
        <input type="text" class="form-control" name="material_size" id="material_size">
    </div>
    <div>
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" name="quantity" id="quantity">
    </div>
    <div>
        <label for="cutting_method" class="form-label">Cutting Method</label>
        <select class="form-select" name="cutting_method" id="cutting_method">
            <option value="">-- Pilih --</option>
            @foreach ($processes as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="sketch" class="form-label">Sket</label>
        <input type="text" class="form-control" name="sketch" id="sketch">
    </div>

    <hr class="grid-full">

    <div class="grid-full d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-primary fw-bold mb-0">Pieceparts</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPiecepart">
            <i class="fas fa-plus me-1"></i> Tambah Piecepart
        </button>
    </div>
    <div class="grid-full table-responsive mb-1">
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

    <hr class="grid-full">

    <h6 class="text-primary fw-bold mb-1 grid-full">Instruksi & Tanggal</h6>

    <div class="grid-full">
        <label for="special_instruction" class="form-label">Special Instruction</label>
        <textarea class="form-control" name="special_instruction" id="special_instruction" rows="2"></textarea>
    </div>
    <div>
        <label for="date_issued" class="form-label">Date Issued</label>
        <input type="date" class="form-control" name="date_issued" id="date_issued">
    </div>
    <div>
        <label for="date_revised" class="form-label">Date Revised</label>
        <input type="date" class="form-control" name="date_revised" id="date_revised">
    </div>
    <div>
        <label for="bpm_number" class="form-label">BPM Number</label>
        <input type="text" class="form-control" name="bpm_number" id="bpm_number">
    </div>
    <div>
        <label for="date_of_bpm" class="form-label">Date of BPM</label>
        <input type="date" class="form-control" name="date_of_bpm" id="date_of_bpm">
    </div>
    <div>
        <label for="prepared_by" class="form-label">Prepared By</label>
        <select class="form-select" name="prepared_by" id="prepared_by">
            <option value="">-- Pilih --</option>
        </select>
    </div>
    <div>
        <label for="approved_by" class="form-label">Approve By</label>
        <select class="form-select" name="approved_by" id="approved_by">
            <option value="">-- Pilih --</option>
        </select>
    </div>
    <div>
        <label for="date_operator" class="form-label">Date Operator</label>
        <select class="form-select" name="date_operator" id="date_operator">
            <option value="">-- Pilih --</option>
        </select>
    </div>
</div>
