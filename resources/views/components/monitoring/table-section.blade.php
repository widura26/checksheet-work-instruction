<div class="st-card p-3 mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-belum-pr" data-status="BELUM PR">
                <label class="form-check-label st-switch-label" for="switch-belum-pr">BELUM PR</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-pr-parsial" data-status="PR PARSIAL">
                <label class="form-check-label st-switch-label" for="switch-pr-parsial">PR PARSIAL</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-sudah-pr" data-status="SUDAH PR">
                <label class="form-check-label st-switch-label" for="switch-sudah-pr">SUDAH PR</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-stok-dan-pr" data-status="STOK DAN PR">
                <label class="form-check-label st-switch-label" for="switch-stok-dan-pr">STOK DAN PR</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-stok" data-status="STOK">
                <label class="form-check-label st-switch-label" for="switch-stok">STOK</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-status-lain" data-status="STATUS LAIN / TIDAK DIKENAL">
                <label class="form-check-label st-switch-label" for="switch-status-lain">STATUS LAIN / TIDAK DIKENAL</label>
            </div>
        </div>
    </div>
</div>

<div class="st-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="st-card-title mb-0">Data Daftar Kebutuhan Material (<span id="bomCountBadge">215</span>)</h5>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-light border py-1 px-3 d-flex align-items-center gap-1 fw-semibold text-secondary" id="btnExportBomCsv" title="Download CSV">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download CSV
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table id="bom-table" class="table table-bordered table-hover align-middle st-table w-100">
            <thead>
                <tr>
                    {{-- <th class="text-center" style="width: 40px;">#</th>
                    <th class="text-center">NO</th>
                    <th>Kode Material Konversi</th>
                    <th>Group</th>
                    <th>Tindak Lanjut</th>
                    <th>Deskripsi Material</th>
                    <th class="text-end">Kebutuhan</th>
                    <th class="text-end">Qty PR (BOM)</th>
                    <th>Kode Material</th>
                    <th>Trainset</th>
                    <th class="text-center">Status PR</th> --}}
                    <th>NO</th>
                    <th>Group</th>
                    <th>Tindak Lanjut</th>
                    <th>Deskripsi Material</th>
                    <th>Kebutuhan</th>
                    <th>Qty PR (BOM)</th>
                    <th>Kode Material</th>
                    <th>Trainset</th>
                    <th>Status PR</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
