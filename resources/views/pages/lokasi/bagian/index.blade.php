@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Bagian</h5>
                    @if (Auth::user()->hasRole('admin'))
                        <a href="{{ route('lokasi.bagian.create') }}" class="btn btn-primary">Tambah Bagian</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover" id="bagian-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bagian</th>
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
            $('#bagian-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lokasi.bagian.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama_bagian',
                        name: 'nama_bagian'
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
