@extends('layouts.app')

@section('title', 'Terima SPM')

@section('content')

    <div class="card">
        <div class="card-header">
            Form Penerimaan Barang untuk SPM: <strong>{{ $spm->no_spm }}</strong>
        </div>
        <div class="card-body">
            @livewire('spm.terima-spm', ['spm' => $spm])
        </div>
    </div>
@endsection
