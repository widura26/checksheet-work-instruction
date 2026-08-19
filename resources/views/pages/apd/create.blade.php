@extends('layouts.app')

@section('title', 'Tambah APD Baru')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Tambah Tool APD Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('apd.store') }}" method="POST">
                @csrf
                @include('pages.apd._form', ['submitButtonText' => 'Tambah'])
            </form>
        </div>
    </div>
@endsection

@push('js')

@endpush