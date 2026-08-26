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
                <i class="fas fa-arrow-left me-1"></i> Kembali
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
            $('#mat').select2({
                placeholder: 'Cari Material...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#crudForm'),
                ajax: {
                    url: "{{ route('cutting-plan.materials') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
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
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-piecepart"><i class="fas fa-trash"></i></button></td>' +
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

            function loadUsers() {
                $.get("{{ route('cutting-plan.users') }}", function(users) {
                    var opts = '<option value="">-- Pilih --</option>';
                    $.each(users, function(i, u) {
                        opts += '<option value="' + u.id + '">' + u.name + '</option>';
                    });
                    $('#prepared_by, #approved_by, #date_operator').html(opts);
                });
            }
            loadUsers();

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
