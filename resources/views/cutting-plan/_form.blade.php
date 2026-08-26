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

    /* Combobox Material: potong teks dengan ellipsis, tanpa scroll horizontal */
    .mat-combobox .dropdown-menu {
        overflow-x: hidden;
    }

    .mat-combobox .dropdown-item {
        display: block;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
        <label for="mat_search" class="form-label">Material</label>
        <div class="position-relative mat-combobox">
            <input type="text" class="form-control" id="mat_search" name="mat_search" placeholder="Cari Material..." autocomplete="off">
            <input type="hidden" name="material_id" id="mat">
            <div class="dropdown-menu w-100" id="mat_dropdown" style="max-height: 240px; overflow-y: auto;"></div>
        </div>
    </div>
    <div>
        <label for="kode_proyek_search" class="form-label">Kode Proyek</label>
        <div class="position-relative mat-combobox">
            <input type="text" class="form-control" id="kode_proyek_search" name="kode_proyek_search" placeholder="Cari Kode Proyek..." autocomplete="off">
            <input type="hidden" name="project_id" id="kode_proyek">
            <div class="dropdown-menu w-100" id="kode_proyek_dropdown" style="max-height: 240px; overflow-y: auto;"></div>
        </div>
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
        <label for="cutting_method_search" class="form-label">Cutting Method</label>
        <div class="position-relative mat-combobox">
            <input type="text" class="form-control" id="cutting_method_search" name="cutting_method_search" placeholder="Cari Cutting Method..." autocomplete="off">
            <input type="hidden" name="process_id" id="cutting_method">
            <div class="dropdown-menu w-100" id="cutting_method_dropdown" style="max-height: 240px; overflow-y: auto;"></div>
        </div>
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
        <label for="prepared_by_search" class="form-label">Prepared By</label>
        <div class="position-relative mat-combobox">
            <input type="text" class="form-control" id="prepared_by_search" name="prepared_by_search" placeholder="Cari User..." autocomplete="off">
            <input type="hidden" name="prepared_by" id="prepared_by">
            <div class="dropdown-menu w-100" id="prepared_by_dropdown" style="max-height: 240px; overflow-y: auto;"></div>
        </div>
    </div>
    <div>
        <label for="approved_by_search" class="form-label">Approve By</label>
        <div class="position-relative mat-combobox">
            <input type="text" class="form-control" id="approved_by_search" name="approved_by_search" placeholder="Cari User..." autocomplete="off">
            <input type="hidden" name="approved_by" id="approved_by">
            <div class="dropdown-menu w-100" id="approved_by_dropdown" style="max-height: 240px; overflow-y: auto;"></div>
        </div>
    </div>
    <div>
        <label for="date_operator_search" class="form-label">Date Operator</label>
        <div class="position-relative mat-combobox">
            <input type="text" class="form-control" id="date_operator_search" name="date_operator_search" placeholder="Cari User..." autocomplete="off">
            <input type="hidden" name="date_operator" id="date_operator">
            <div class="dropdown-menu w-100" id="date_operator_dropdown" style="max-height: 240px; overflow-y: auto;"></div>
        </div>
    </div>
</div>
