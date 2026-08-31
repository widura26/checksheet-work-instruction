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

    .material-section {
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f8f9fa;
    }

    .material-section .material-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .material-section .material-header h6 {
        margin-bottom: 0;
        color: #0d6efd;
        font-weight: 600;
    }

    .parts-section {
        margin-top: 0.75rem;
    }

    .parts-section .parts-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
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
        <label for="project_search" class="form-label">Kode Proyek</label>
        <div class="position-relative mat-combobox">
            <input type="text" class="form-control" id="project_search" name="project_search" placeholder="Cari Proyek..." autocomplete="off">
            <input type="hidden" name="project_id" id="project_id">
            <div class="dropdown-menu w-100" id="project_dropdown" style="max-height: 240px; overflow-y: auto;"></div>
        </div>
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
        <h6 class="text-primary fw-bold mb-0">Materials</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddMaterial">
            <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg> Tambah Material
        </button>
    </div>

    <div id="materialsContainer" class="grid-full"></div>

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
