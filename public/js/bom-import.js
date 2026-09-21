$(document).ready(function() {
    let currentImportId = null;
    let currentFileName = null;

    // Upload file
    $('#btnUpload').on('click', function() {
        const file = $('#bomFile')[0].files[0];
        if (!file) return;

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

                // Show sheet list
                let html = '';
                response.sheets.forEach(sheet => {
                    html += `<div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${sheet}" id="sheet-${sheet}">
                        <label class="form-check-label" for="sheet-${sheet}">${sheet}</label>
                    </div>`;
                });
                $('#sheetList').html(html);
                $('#uploadSection').hide();
                $('#sheetSelection').show();
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
                    html += `<h6>${sheetName}</h6>`;
                    html += '<table class="table table-sm table-bordered">';
                    rows.forEach((row, index) => {
                        html += '<tr>';
                        row.forEach(cell => {
                            if (index === 0) {
                                html += `<th>${cell || ''}</th>`;
                            } else {
                                html += `<td>${cell || ''}</td>`;
                            }
                        });
                        html += '</tr>';
                    });
                    html += '</table>';
                }
                $('#previewContent').html(html);
                $('#sheetSelection').hide();
                $('#previewSection').show();
            }
        });
    });

    // Import from preview
    $('#btnImportFromPreview').on('click', function() {
        importSheets(getSelectedSheets());
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
                $('#importExcelModal').modal('hide');
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
