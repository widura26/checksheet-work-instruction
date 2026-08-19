@extends('layouts.app')

@section('title', 'Daftar Posisi CT')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Daftar Posisi CT</h5>
                <small class="text-body-secondary">Halaman ini menampilkan daftar semua posisi Central Tool</small>
            </div>
            <a class="btn btn-primary" href="{{ route('posisi-ct.create') }}">
                <svg class="icon me-2">
                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-plus') }}"></use>
                </svg>
                Tambah Posisi
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Posisi CT</th>
                            <th>Departemen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_posisi_ct }}</td>
                                <td>{{ $item->departemen ? $item->departemen->nama_departemen : '(UU) Stok Bebas' }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('posisi-ct.edit', $item->id) }}"
                                            class="btn btn-sm btn-info me-2 text-white">
                                            Edit
                                        </a>
                                        <form action="{{ route('posisi-ct.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Data tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
