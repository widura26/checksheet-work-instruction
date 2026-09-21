document.addEventListener('DOMContentLoaded', function() {
    let currentImportId = null;
    let currentFileName = null;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // Upload file
    document.getElementById('btnUpload').addEventListener('click', function() {
        const fileInput = document.getElementById('bomFile');
        const file = fileInput.files[0];
        if (!file) return;

        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Uploading...';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', getCsrfToken());

        fetch('/bom/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentImportId = data.import_id;
                currentFileName = data.file_name;

                let html = '';
                data.sheets.forEach(sheet => {
                    html += `<div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${escapeHtml(sheet)}" id="sheet-${escapeHtml(sheet)}">
                        <label class="form-check-label" for="sheet-${escapeHtml(sheet)}">${escapeHtml(sheet)}</label>
                    </div>`;
                });
                document.getElementById('sheetList').innerHTML = html;
                document.getElementById('uploadSection').style.display = 'none';
                document.getElementById('sheetSelection').style.display = 'block';
            } else {
                alert('Terjadi kesalahan: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Upload';
        });
    });

    // Preview
    document.getElementById('btnPreview').addEventListener('click', function() {
        const selectedSheets = getSelectedSheets();
        if (selectedSheets.length === 0) {
            alert('Pilih minimal 1 sheet');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Loading...';

        fetch('/bom/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                import_id: currentImportId,
                file_name: currentFileName,
                sheets: selectedSheets
            })
        })
        .then(response => response.json())
        .then(data => {
            let html = '';
            for (const [sheetName, rows] of Object.entries(data.preview)) {
                html += `<h6>${escapeHtml(sheetName)}</h6>`;
                html += '<table class="table table-sm table-bordered">';
                rows.forEach((row, index) => {
                    html += '<tr>';
                    row.forEach(cell => {
                        if (index === 0) {
                            html += `<th>${escapeHtml(cell || '')}</th>`;
                        } else {
                            html += `<td>${escapeHtml(cell || '')}</td>`;
                        }
                    });
                    html += '</tr>';
                });
                html += '</table>';
            }
            document.getElementById('previewContent').innerHTML = html;
            document.getElementById('sheetSelection').style.display = 'none';
            document.getElementById('previewSection').style.display = 'block';
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Preview';
        });
    });

    // Import from preview
    document.getElementById('btnImportFromPreview').addEventListener('click', function() {
        const selectedSheets = getSelectedSheets();
        if (selectedSheets.length === 0) {
            alert('Pilih minimal 1 sheet');
            return;
        }
        importSheets(selectedSheets);
    });

    // Import directly
    document.getElementById('btnImportSheets').addEventListener('click', function() {
        const selectedSheets = getSelectedSheets();
        if (selectedSheets.length === 0) {
            alert('Pilih minimal 1 sheet');
            return;
        }
        importSheets(selectedSheets);
    });

    // Back to sheet selection
    document.getElementById('btnBack').addEventListener('click', function() {
        document.getElementById('previewSection').style.display = 'none';
        document.getElementById('sheetSelection').style.display = 'block';
    });

    function importSheets(sheets) {
        const btnImportFromPreview = document.getElementById('btnImportFromPreview');
        const btnImportSheets = document.getElementById('btnImportSheets');

        btnImportFromPreview.disabled = true;
        btnImportFromPreview.textContent = 'Importing...';
        btnImportSheets.disabled = true;
        btnImportSheets.textContent = 'Importing...';

        fetch('/bom/import-sheets', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                import_id: currentImportId,
                file_name: currentFileName,
                sheets: sheets
            })
        })
        .then(response => response.json())
        .then(data => {
            alert('Import dimulai! Gunakan import_id untuk cek status: ' + data.import_id);
            const importModal = new coreui.Modal(document.getElementById('importExcelModal'));
            importModal.hide();
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            btnImportFromPreview.disabled = false;
            btnImportFromPreview.textContent = 'Import';
            btnImportSheets.disabled = false;
            btnImportSheets.textContent = 'Import';
        });
    }

    function getSelectedSheets() {
        const sheets = [];
        document.querySelectorAll('#sheetList input:checked').forEach(checkbox => {
            sheets.push(checkbox.value);
        });
        return sheets;
    }
});
