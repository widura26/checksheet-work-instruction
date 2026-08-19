@extends('layouts.app')
@section('title', 'Tambah/Update Stok')
@push('css')
    <style>
        .loading .form-control {
            background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='none' fill-rule='evenodd'%3e%3cg fill='%236c757d' fill-opacity='0.3'%3e%3cpath d='M10 3v3l4-4-4-4v3c-4.42 0-8 3.58-8 8s3.58 8 8 8c1.57 0 3.04-.46 4.28-1.25l1.45 1.45C14.1 18.81 12.15 19.5 10 19.5c-5.52 0-10-4.48-10-10S4.48-.5 10-.5z'/%3e%3c/g%3e%3c/g%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 16px;
        }

        .readonly-field,
        .auto-populated[readonly] {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .form-mode-badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            transition: all 0.3s ease;
        }

        .form-mode-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .loading-indicator {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .loading-spinner {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .create-only-field,
        .update-only-field {
            transition: all 0.3s ease;
        }

        .create-only-field.fade-in,
        .update-only-field.fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        .vendor-required {
            transition: all 0.3s ease;
        }

        .retry-btn {
            margin-top: 0.25rem;
            margin-left: 0.5rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Visual indicators for form states */
        .form-mode-badge.bg-success {
            background-color: #198754 !important;
        }

        .form-mode-badge.bg-info {
            background-color: #0dcaf0 !important;
        }

        .form-mode-badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        /* Enhanced field styling */
        .form-label .text-danger {
            font-weight: 600;
        }

        .border-bottom {
            border-bottom: 2px solid #dee2e6 !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        /* Stock display styling */
        #current_stock_display {
            max-height: 300px;
            overflow-y: auto;
        }

        #current_stock_display .border-bottom:last-child {
            border-bottom: none !important;
        }

        .stock-project-item {
            transition: background-color 0.2s ease;
        }

        .stock-project-item:hover {
            background-color: #f8f9fa;
        }

        .stock-total {
            background-color: #e3f2fd !important;
            border: 1px solid #2196f3 !important;
        }

        /* Project quantity inputs styling */
        .project-quantity-input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
        }

        .project-quantity-input.create-mode {
            background-color: #e8f5e8;
            border-color: #198754;
        }

        .project-quantity-input.update-mode {
            background-color: #e3f2fd;
            border-color: #0dcaf0;
        }

        .project-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .current-stock-badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush
@section('content')

    <livewire:material.materials-create />

@endsection

@push('js')
@endpush
