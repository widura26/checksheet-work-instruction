@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <small class="text-muted">{{ date('d F Y') }} </small>
        </div>
        <livewire:charts.dashboard>
            <style>
                .border-left-primary {
                    border-left: 0.25rem solid #4e73df !important;
                }

                .border-left-success {
                    border-left: 0.25rem solid #1cc88a !important;
                }

                .border-left-info {
                    border-left: 0.25rem solid #36b9cc !important;
                }

                .border-left-warning {
                    border-left: 0.25rem solid #f6c23e !important;
                }

                .border-left-danger {
                    border-left: 0.25rem solid #e74a3b !important;
                }

                .border-left-secondary {
                    border-left: 0.25rem solid #858796 !important;
                }

                .border-left-dark {
                    border-left: 0.25rem solid #5a5c69 !important;
                }

                .text-xs {
                    font-size: 0.7rem;
                }

                .shadow {
                    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
                }

                .text-gray-800 {
                    color: #5a5c69 !important;
                }

                .text-gray-300 {
                    color: #dddfeb !important;
                }
            </style>
        @endsection
        @push('js')

        @endpush
