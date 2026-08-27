@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')

    <div class="card mb-4">
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
            <div class="d-flex justify-content-end mb-3 gap-2">
                @if (Auth::user()->hasRole('admin'))
                    <button type="button" class="btn btn-success text-white" data-coreui-toggle="modal"
                        data-coreui-target="#importExcelModal">
                        <i class="fas fa-plus me-1"></i>
                        Import Excel
                    </button>
                @endif
            </div>

            <div class="table-responsive">
                <table id="projects-table" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Project ID</th>
                            <th>Nama Project</th>
                            @if (Auth::user()->hasRole('admin'))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
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
    </div>
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
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('projects.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }, {
                        data: 'project_id',
                        name: 'project_id'
                    },
                    {
                        data: 'nama_project',
                        name: 'nama_project'
                    },
                    @if (Auth::user()->hasRole('admin'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    @endif
                ]
            });
        });
    </script>
@endpush
