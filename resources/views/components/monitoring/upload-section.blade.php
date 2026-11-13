<div class="st-expander">
    <div class="st-expander-header" id="expanderToggle">
        <div class="d-flex align-items-center gap-2">
            <svg class="st-expander-chevron" id="expanderChevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(-90deg);">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
            <span class="fw-semibold" style="font-size: 15px; color: #31333F;">Upload Daftar Kebutuhan Material</span>
        </div>
        <span class="text-muted small">Excel (.xlsx / .xls)</span>
    </div>
    <div class="st-expander-body" id="expanderBody" style="display: none;">
        <!-- Kriteria File -->
        <div class="mb-3 p-3 rounded-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
            <h6 class="fw-bold mb-2 text-dark" style="font-size: 13px;">Kriteria File:</h6>
            <ul class="mb-0 text-muted small ps-3">
                <li class="mb-1"><strong>Format:</strong> Excel (<code>.xlsx</code> / <code>.xls</code>) maksimal <strong>25 MB</strong>.</li>
                <li>Gunakan <a href="https://docs.google.com/spreadsheets/d/1b3kH9CyGzWNEiYSRL7enafyEO8jhaGVbz7DuTQrOZ1k/edit?usp=sharing" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none fw-semibold">Template</a> agar kolom terdeteksi dengan baik.</li>
            </ul>
        </div>

        <!-- Upload Area -->
        <div class="mb-3">
            <label class="form-label text-dark fw-semibold small d-flex align-items-center gap-1 mb-2">
                Pilih File
                <span class="text-muted" title="Upload file Excel BOM"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span>
            </label>

            <!-- Dropzone -->
            <div class="st-dropzone p-4 text-center rounded-3" id="bomDropzone">
                <input type="file" id="bomFileInput" class="d-none" accept=".xlsx,.xls,.csv">
                <div class="d-flex flex-column align-items-center justify-content-center py-2">
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-semibold" onclick="document.getElementById('bomFileInput').click()">
                            <svg width="16" height="16" class="me-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            Upload
                        </button>
                    </div>
                    <div class="text-secondary small" id="bomFileLabel">25MB per file • XLS, XLSX</div>
                </div>
            </div>
        </div>

        <!-- Action button and info -->
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <button type="button" class="btn btn-primary text-white fw-semibold px-4 py-2 rounded-2" id="btnBomGenerate" style="background-color: #0068c9; border-color: #0068c9;">
                Generate & Rekap Data
            </button>
            <span class="text-muted small" id="bomStatusText">Belum ada file diunggah.</span>
        </div>

        <!-- Progress / Result container -->
        <div id="bomUploadProgress" class="d-none mt-3">
            <div class="progress" style="height: 6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 100%"></div>
            </div>
        </div>
        <div id="bomUploadResult" class="d-none mt-3"></div>
    </div>
</div>
