@extends('layouts.app')

@section('title', 'Edit Consumable')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Tool Consumable</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('consumable.update', $tool->public_id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pages.consumable._form', ['submitButtonText' => 'Update'])
            </form>
        </div>
    </div>
@endsection

@push('js')

@endpush