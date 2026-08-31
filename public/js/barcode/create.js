$(function() {
    var modal = new coreui.Modal(document.getElementById('barcodeModal'));
    var $partsWrap = $('#partsWrap');
    var $partsList = $('#partsList');

    function loadParts(cuttingPlanId) {
        if (!cuttingPlanId) {
            $partsWrap.addClass('d-none');
            $partsList.empty();
            window.__partsData = {};
            return;
        }

        $.get(BC.routes.parts, { cutting_plan_id: cuttingPlanId }, function(res) {
            $partsList.empty();
            window.__partsData = {};

            $.each(res.data, function(i, p) {
                window.__partsData[p.id] = p;
            });

            if (!res.data.length) {
                $partsWrap.removeClass('d-none');
                $partsList.append(
                    '<tr><td colspan="5" class="text-center text-muted">Tidak ada part untuk cutting plan ini.</td></tr>'
                );
                return;
            }

            $.each(res.data, function(i, p) {
                var hasBarcode = !!p.barcode_id;
                var badge = hasBarcode
                    ? '<span class="badge bg-success">Ada</span>'
                    : '<span class="badge bg-secondary">Belum</span>';
                var btn = hasBarcode
                    ? '<button type="button" class="btn btn-warning btn-sm btn-barcode" data-part="' + p.id + '">Edit Barcode</button>'
                    : '<button type="button" class="btn btn-primary btn-sm btn-barcode" data-part="' + p.id + '">Tambah Barcode</button>';

                var row = '<tr>' +
                    '<td>' + (p.part_number || '') + '</td>' +
                    '<td>' + (p.cutting_size || '') + '</td>' +
                    '<td>' + (p.part_name || '') + '</td>' +
                    '<td class="text-center">' + badge + '</td>' +
                    '<td class="text-center">' + btn + '</td>' +
                    '</tr>';

                $partsList.append(row);
            });

            $partsWrap.removeClass('d-none');
        });
    }

    $('#cutting_plan_id').on('change', function() {
        loadParts($(this).val());
    });

    // Pre-load jika dibuka dari menu Cutting Plan (?cutting_plan_id=...)
    var initialCp = $('#cutting_plan_id').val();
    if (initialCp) {
        loadParts(initialCp);
    }

    $partsList.on('click', '.btn-barcode', function() {
        var partId = $(this).data('part');
        var $cp = $('#cutting_plan_id');
        var cuttingPlanId = $cp.val();

        // Cari data part dari baris yang dirender
        var $row = $(this).closest('tr');
        var partLabel = $row.find('td').eq(0).text() +
            ($row.find('td').eq(2).text() ? ' - ' + $row.find('td').eq(2).text() : '');

        $('#barcodeError').addClass('d-none').text('');
        $('#f_cutting_plan_id').val(cuttingPlanId);
        $('#f_part_id').val(partId);
        $('#f_part_label').val(partLabel);

        // Cari nilai barcode yang mungkin sudah ada (dari data yang dirender)
        var part = window.__partsData ? window.__partsData[partId] : null;

        if (part && part.barcode_id) {
            $('#barcode_method').val('PUT');
            $('#f_barcode_id').val(part.barcode_id);
            $('#f_wbs').val(part.barcode_wbs || '');
            $('#f_lot').val(part.barcode_lot || '');
            $('#f_part_no').val(part.barcode_part_no || part.part_number || '');
            $('#barcodeModalLabel').text('Edit Barcode');
        } else {
            $('#barcode_method').val('POST');
            $('#f_barcode_id').val('');
            $('#f_wbs').val('');
            $('#f_lot').val('');
            $('#f_part_no').val(part ? part.part_number : '');
            $('#barcodeModalLabel').text('Tambah Barcode');
        }

        modal.show();
    });

    $('#barcodeForm').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var btn = $('#btnBarcodeSubmit');
        btn.prop('disabled', true).text('Menyimpan...');

        var barcodeId = $('#f_barcode_id').val();
        var url = barcodeId ? BC.routes.update.replace('__ID__', barcodeId) : BC.routes.store;
        var method = barcodeId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: new FormData(form),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                btn.prop('disabled', false).text('Simpan');
                modal.hide();
                Swal.fire('Berhasil', res.message, 'success');
                loadParts($('#cutting_plan_id').val());
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var msg = '<ul class="mb-0">';
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        msg += '<li>' + v[0] + '</li>';
                    });
                    msg += '</ul>';
                    $('#barcodeError').removeClass('d-none').html(msg);
                } else {
                    $('#barcodeError').removeClass('d-none').text('Terjadi kesalahan saat menyimpan data.');
                }
            }
        });
    });
});
