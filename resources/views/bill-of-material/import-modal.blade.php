<!-- Modal Import BOM -->
<div class="modal fade" id="bomImportModal" tabindex="-1" aria-labelledby="bomImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bomImportModalLabel">Import Bill of Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Upload File -->
                <div id="uploadSection">
                    <div class="mb-3">
                        <label for="bomFile" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" id="bomFile" accept=".xlsx,.xls" required>
                        <div class="form-text">Format: .xlsx atau .xls</div>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnUpload">Upload</button>
                </div>

                <!-- Step 2: Select Sheets -->
                <div id="sheetSelection" style="display: none;">
                    <h6>Sheet tersedia:</h6>
                    <div id="sheetList"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-info" id="btnPreview">Preview</button>
                        <button type="button" class="btn btn-success" id="btnImportSheets">Import Langsung</button>
                    </div>
                </div>

                <!-- Step 3: Preview -->
                <div id="previewSection" style="display: none;">
                    <h6 id="previewTitle">Preview</h6>
                    <div id="previewContent"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" id="btnImportFromPreview">Import Sheet Ini</button>
                        <button type="button" class="btn btn-secondary" id="btnBack">Kembali</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
