@extends('layouts.app')

@section('title', 'Tambah Cutting Plan')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Tambah Cutting Plan</h5>
                <small class="text-body-secondary">Form input data Cutting Plan (CP)</small>
            </div>
            <a href="{{ route('cutting-plan.index') }}" class="btn btn-secondary">
                <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M23.312 11.25h-19.558l4.5-4.5-1.061-1.061-6.31 6.31 6.31 6.31 1.061-1.061-4.5-4.5h19.558v-1.5z"></path></svg> Kembali
            </a>
        </div>
        <div class="card-body">
            <form id="crudForm" action="{{ route('cutting-plan.store') }}" method="POST">
                @csrf
                @include('cutting-plan._form')

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('cutting-plan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(function() {
            function initCombobox(opts) {
                var $input = $(opts.input);
                var $hidden = $(opts.hidden);
                var $dropdown = $(opts.dropdown);
                var $wrapper = $hidden.closest('.mat-combobox');
                var results = [];

                function render(rs) {
                    results = rs || [];
                    var html = '';
                    if (!results.length) {
                        html = '<div class="dropdown-item text-muted disabled">Tidak ada hasil</div>';
                    } else {
                        $.each(results, function(i, m) {
                            html += '<button type="button" class="dropdown-item" data-id="' + m.id +
                                '">' + m.text + '</button>';
                        });
                    }
                    $dropdown.html(html).addClass('show');
                }

                function search(q) {
                    $.get(opts.url, {
                        q: q || ''
                    }, function(data) {
                        render(data.results || []);
                    });
                }

                var timer;
                $input.on('input', function() {
                    $hidden.val('');
                    clearTimeout(timer);
                    var val = $(this).val();
                    timer = setTimeout(function() {
                        search(val);
                    }, 250);
                }).on('focus', function() {
                    if (!$dropdown.hasClass('show')) {
                        search($input.val());
                    }
                });

                $dropdown.on('click', '.dropdown-item', function() {
                    if ($(this).hasClass('disabled')) return;
                    var id = $(this).data('id');
                    var found = results.find(function(m) {
                        return String(m.id) === String(id);
                    });
                    $input.val(found ? found.text : '');
                    $hidden.val(id);
                    $dropdown.removeClass('show');
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest($wrapper).length) {
                        $dropdown.removeClass('show');
                    }
                });
            }

            initCombobox({
                input: '#mat_search',
                hidden: '#mat',
                dropdown: '#mat_dropdown',
                url: "{{ route('cutting-plan.materials') }}"
            });
            initCombobox({
                input: '#kode_proyek_search',
                hidden: '#kode_proyek',
                dropdown: '#kode_proyek_dropdown',
                url: "{{ route('cutting-plan.projects') }}"
            });
            initCombobox({
                input: '#cutting_method_search',
                hidden: '#cutting_method',
                dropdown: '#cutting_method_dropdown',
                url: "{{ route('cutting-plan.processes') }}"
            });
            initCombobox({
                input: '#prepared_by_search',
                hidden: '#prepared_by',
                dropdown: '#prepared_by_dropdown',
                url: "{{ route('cutting-plan.users') }}"
            });
            initCombobox({
                input: '#approved_by_search',
                hidden: '#approved_by',
                dropdown: '#approved_by_dropdown',
                url: "{{ route('cutting-plan.users') }}"
            });
            initCombobox({
                input: '#date_operator_search',
                hidden: '#date_operator',
                dropdown: '#date_operator_dropdown',
                url: "{{ route('cutting-plan.users') }}"
            });

            var piecepartTemplate = function(index, data) {
                data = data || {};
                return '<tr>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][part_no]" value="' + (data.part_no ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][cutting_size]" value="' + (data.cutting_size ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][qty_car]" value="' + (data.qty_car ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][qty_lot]" value="' + (data.qty_lot ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][part_name]" value="' + (data.part_name ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][remark]" value="' + (data.remark ?? '') + '"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-piecepart"><svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M15.375 4.125v-2.25c0-0.631-0.445-1.125-1.013-1.125h-4.725c-0.568 0-1.013 0.494-1.013 1.125v2.25h-5.625v1.5h18v-1.5zM10.125 2.25h3.75v1.875h-3.75z"></path></svg></button></td>' +
                    '</tr>';
            };

            function reindexPieceparts() {
                $('#piecepartsList tr').each(function(i) {
                    $(this).find('input').each(function() {
                        var name = $(this).attr('name');
                        var newName = name.replace(/pieceparts\[\d+\]/, 'pieceparts[' + i + ']');
                        $(this).attr('name', newName);
                    });
                });
            }

            function addPiecepart(data) {
                var index = $('#piecepartsList tr').length;
                $('#piecepartsList').append(piecepartTemplate(index, data));
            }

            $('#btnAddPiecepart').on('click', function() {
                addPiecepart();
            });

            $('#piecepartsList').on('click', '.btn-remove-piecepart', function() {
                $(this).closest('tr').remove();
                reindexPieceparts();
            });

            addPiecepart();

            $('#crudForm').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var btn = $('#btnSubmit');
                btn.prop('disabled', true).text('Menyimpan...');
                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire('Berhasil', res.message, 'success').then(function() {
                            window.location = "{{ route('cutting-plan.index') }}";
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
                            $('#formError').removeClass('d-none').html(msg);
                        } else {
                            $('#formError').removeClass('d-none').text(
                                `Terjadi kesalahan saat menyimpan data. ${xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : ''}`);
                        }
                    }
                });
            });
        });
    </script>
@endpush
