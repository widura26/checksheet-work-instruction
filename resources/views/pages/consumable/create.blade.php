@extends('layouts.app')

@section('title', 'Tambah Consumable Baru')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Tambah Tool Consumable Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('consumable.store') }}" method="POST">
                @csrf
                @include('pages.consumable._form', ['submitButtonText' => 'Tambah'])
            </form>
        </div>
    </div>
@endsection

@push('js')

@endpush