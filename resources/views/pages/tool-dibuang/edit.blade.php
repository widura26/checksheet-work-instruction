@extends('layouts.app')

@section('title', 'Pendataan Tool Dibuang')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-md-12">
                    @livewire('tool-dibuang.tool-dibuang-edit', ['daftarToolDibuang' => $toolDibuang, 'noBs' => $noBs, 'toolDibuangId' => $toolDibuangId])
                </div>
            </div>
        </div>
    </div>
@endsection
