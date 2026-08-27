@extends('layouts.app')

@section('title', 'Daftar Proyek')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }

        #projects-table {
            width: 100% !important;
        }

        .icon {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')

    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Daftar Proyek</h5>
            </div>
            @if (Auth::user()->hasRole('admin'))
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-success text-white" data-coreui-toggle="modal"
                        data-coreui-target="#importExcelModal">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg>
                        Import Excel
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-responsive">
                <table id="projects-table" class="table table-bordered table-hover" style="width: 100%;">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Proyek</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    {{-- <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">Import Excel Project</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('projects.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Pilih File Excel</label>
                            <input class="form-control" type="file" name="excel_file" id="excel_file"
                                accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                                required>
                            <div class="form-text">Format file yang didukung: .xlsx, .xls</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success text-white">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
@endsection

@push('js')
    <script type="module">
        document.addEventListener('click', function(e) {
            let button = e.target.closest('.btn-close-project');
            if (!button) return;
            let form = button.closest('form');
            if (button.dataset.submitted === "true") return;
            Swal.fire({
                title: 'Apakah Anda Yakin ?',
                text: "Project akan ditutup dan tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, tutup!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.dataset.submitted = "true";
                    button.disabled = true;
                    button.innerHTML = 'Processing...';
                    form.submit();
                }
            });
        });
        $(function() {
                $('#projects-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: true,
                    scrollCollapse: true,
                    ajax: function (data, callback, settings) {
                        $.get("{{ route('project-code.data') }}", {
                            page: (data.start / data.length) + 1,
                            per_page: data.length,
                            search: data.search.value
                        }).done(function (json) {
                            callback({
                                draw: data.draw,
                                recordsTotal: json.total,
                                recordsFiltered: json.total,
                                data: json.data
                            });
                        });
                    },
                    columns: [{
                            data: null,
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function (data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            data: 'project_code',
                            name: 'project_code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="/project-code/' + data + '/edit" class="btn btn-sm btn-primary">' +
                                    '<i class="fas fa-edit me-1"></i> Edit</a>';
                            }
                        }
                    ]
                });
        });
    </script>
@endpush
