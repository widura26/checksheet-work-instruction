$(document).ready(function() {
    let currentImportId = null;
    let currentFileName = null;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Upload file
    $('#btnUpload').on('click', function() {
        const file = $('#bomFile')[0].files[0];
        if (!file) return;

        const $btn = $(this);
        $btn.prop('disabled', true).text('Uploading...');

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '/bom/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                currentImportId = response.import_id;
                currentFileName = response.file_name;

                let html = '';
                response.sheets.forEach(sheet => {
                    html += `<div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${escapeHtml(sheet)}" id="sheet-${escapeHtml(sheet)}">
                        <label class="form-check-label" for="sheet-${escapeHtml(sheet)}">${escapeHtml(sheet)}</label>
                    </div>`;
                });
                $('#sheetList').html(html);
                $('#uploadSection').hide();
                $('#sheetSelection').show();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText));
            },
            complete: function() {
                $btn.prop('disabled', false).text('Upload');
            }
        });
    });

    // Preview
    $('#btnPreview').on('click', function() {
        const selectedSheets = getSelectedSheets();
        if (selectedSheets.length === 0) {
            alert('Pilih minimal 1 sheet');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).text('Loading...');

        $.ajax({
            url: '/bom/preview',
            type: 'POST',
            data: {
                import_id: currentImportId,
                file_name: currentFileName,
                sheets: selectedSheets,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let html = '';
                for (const [sheetName, rows] of Object.entries(response.preview)) {
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
                $('#previewContent').html(html);
                $('#sheetSelection').hide();
                $('#previewSection').show();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText));
            },
            complete: function() {
                $btn.prop('disabled', false).text('Preview');
            }
        });
    });

    // Import from preview
    $('#btnImportFromPreview').on('click', function() {
        const selectedSheets = getSelectedSheets();
        if (selectedSheets.length === 0) {
            alert('Pilih minimal 1 sheet');
            return;
        }
        importSheets(selectedSheets);
    });

    // Import directly
    $('#btnImportSheets').on('click', function() {
        const selectedSheets = getSelectedSheets();
        if (selectedSheets.length === 0) {
            alert('Pilih minimal 1 sheet');
            return;
        }
        importSheets(selectedSheets);
    });

    // Back to sheet selection
    $('#btnBack').on('click', function() {
        $('#previewSection').hide();
        $('#sheetSelection').show();
    });

    function importSheets(sheets) {
        const $btn = $('#btnImportFromPreview, #btnImportSheets');
        $btn.prop('disabled', true).text('Importing...');

        $.ajax({
            url: '/bom/import-sheets',
            type: 'POST',
            data: {
                import_id: currentImportId,
                file_name: currentFileName,
                sheets: sheets,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Import dimulai! Gunakan import_id untuk cek status: ' + response.import_id);
                const importModal = new coreui.Modal(document.getElementById('importExcelModal'));
                importModal.hide();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText));
            },
            complete: function() {
                $btn.prop('disabled', false).text('Import');
            }
        });
    }

    function getSelectedSheets() {
        const sheets = [];
        $('#sheetList input:checked').each(function() {
            sheets.push($(this).val());
        });
        return sheets;
    }
});
