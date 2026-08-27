@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Unit</h5>
                    @if (Auth::user()->hasRole('admin'))
                        <a href="{{ route('lokasi.unit.create') }}" class="btn btn-primary">Tambah Unit</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="unit-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Unit</th>
                            @if (Auth::user()->hasRole('admin'))
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(function() {
            $('#unit-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('lokasi.unit.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_unit',
                        name: 'nama_unit'
                    },
                    @if (Auth::user()->hasRole('admin'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ]
            });
        });
    </script>
@endpush
