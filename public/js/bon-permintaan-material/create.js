$(function() {
    var $cp = $('#cutting_plan_id');
    var $material = $('#material_id');

    function syncMaterial() {
        var cpId = $cp.val();
        var mat = cpId && window.BPM.cpMaterials
            ? window.BPM.cpMaterials[cpId]
            : null;

        $material.empty();

        if (!mat) {
            $material.append($('<option>', {
                value: '',
                text: '-- Pilih Cutting Plan terlebih dahulu --'
            }));
            return;
        }

        $material.append($('<option>', {
            value: mat.id,
            text: mat.label,
            selected: true
        }));
    }

    $cp.on('change', syncMaterial);

    // Pre-load jika dibuka dari menu Cutting Plan (?cutting_plan_id=...)
    if ($cp.val()) {
        syncMaterial();
    }

    $('#bpmForm').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var btn = $('#btnSubmit');
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: BPM.routes.store,
            type: 'POST',
            data: new FormData(form),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                btn.prop('disabled', false).text('Simpan');
                Swal.fire('Berhasil', res.message, 'success').then(function() {
                    if (res.redirect) {
                        window.location.href = res.redirect;
                    }
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var msg = '<ul class="mb-0">';
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        msg += '<li>' + v[0] + '</li>';
                    });
                    msg += '</ul>';
                    $('#bpmError').removeClass('d-none').html(msg);
                } else {
                    $('#bpmError').removeClass('d-none').text('Terjadi kesalahan saat menyimpan data.');
                }
            }
        });
    });
});
