@extends('layouts.app')

@section('title', 'Edit APD')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Tool APD</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('apd.update', $tool->public_id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pages.apd._form', ['submitButtonText' => 'Update'])
            </form>
        </div>
    </div>
@endsection

@push('js')

@endpush
