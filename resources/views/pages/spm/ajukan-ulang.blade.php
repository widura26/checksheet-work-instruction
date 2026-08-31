@extends('layouts.app')

@section('title', 'Ajukan Ulang SPM')

@section('content')
    @livewire('spm.resubmit-spm', ['spm' => $spm])
@endsection
