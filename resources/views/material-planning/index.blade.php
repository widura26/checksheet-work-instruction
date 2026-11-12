@extends('layouts.app')

@section('title', 'Dashboard Monitoring Purchase Requisition')

@push('css')
    <style>
        /* Override page background to clean white */
        :root {
            --cui-body-bg: #ffffff;
            --cui-tertiary-bg: #ffffff;
            --cui-body-bg-rgb: 255, 255, 255;
            --cui-tertiary-bg-rgb: 255, 255, 255;
        }
        body {
            background-color: #ffffff !important;
        }

        .st-dashboard {
            font-family: "Source Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #31333F;
            padding-bottom: 3rem;
        }

        .st-dashboard h1, .st-dashboard h2, .st-dashboard h3, .st-dashboard h4, .st-dashboard h5 {
            color: #31333F;
            font-family: inherit;
        }

        .st-page-title {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
            color: #262730;
        }

        /* Streamlit Expander */
        .st-expander {
            border: 1px solid rgba(49, 51, 63, 0.2);
            border-radius: 8px;
            background: #ffffff;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .st-expander-header {
            padding: 12px 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            user-select: none;
            transition: background-color 0.15s;
        }

        .st-expander-header:hover {
            background-color: #f8f9fa;
        }

        .st-expander-chevron {
            transition: transform 0.2s ease;
            color: #495057;
        }

        .st-expander-body {
            border-top: 1px solid rgba(49, 51, 63, 0.12);
            padding: 18px;
            background: #ffffff;
        }

        /* Dropzone */
        .st-dropzone {
            border: 1px dashed rgba(49, 51, 63, 0.3);
            border-radius: 8px;
            background: #ffffff;
            transition: border-color 0.2s, background-color 0.2s;
        }

        .st-dropzone.dragover {
            border-color: #0068c9;
            background-color: #f0f7ff;
        }

        /* Streamlit Select */
        .st-select-label {
            font-size: 14px;
            font-weight: 500;
            color: #31333F;
            margin-bottom: 6px;
        }

        .st-select {
            background-color: #ffffff;
            border: 1px solid rgba(49, 51, 63, 0.2);
            border-radius: 8px;
            color: #31333F;
            font-size: 14px;
            font-weight: 400;
            height: 42px;
            padding: 8px 14px;
            transition: all 0.2s;
        }

        .st-select:focus {
            background-color: #ffffff;
            border-color: #ff4b4b;
            box-shadow: 0 0 0 1px #ff4b4b;
            outline: none;
        }

        /* Streamlit Metric Card */
        .st-metric-card {
            background: #ffffff;
            border: 1px solid rgba(49, 51, 63, 0.2);
            border-radius: 8px;
            padding: 18px 24px;
            height: 100%;
        }

        .st-metric-label {
            font-size: 14px;
            color: #31333F;
            margin-bottom: 4px;
            font-weight: 400;
        }

        .st-metric-value {
            font-size: 2.75rem;
            font-weight: 700;
            color: #31333F;
            line-height: 1.1;
        }

        /* Streamlit Card Container */
        .st-card {
            background: #ffffff;
            border: 1px solid rgba(49, 51, 63, 0.2);
            border-radius: 8px;
            padding: 20px;
        }

        .st-card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #31333F;
            margin-bottom: 1rem;
        }

        /* Streamlit Switch Toggle */
        .form-check-input.st-switch {
            width: 38px;
            height: 20px;
            cursor: pointer;
            border-color: rgba(49, 51, 63, 0.3);
        }

        .form-check-input.st-switch:checked {
            background-color: #ff4b4b;
            border-color: #ff4b4b;
        }

        .form-check-input.st-switch:focus {
            box-shadow: 0 0 0 1px #ff4b4b;
            border-color: #ff4b4b;
        }

        .st-switch-label {
            font-size: 14px;
            font-weight: 500;
            color: #31333F;
            cursor: pointer;
            user-select: none;
        }

        /* Streamlit Table */
        .st-table {
            font-size: 13px;
            border-collapse: collapse;
        }

        .st-table thead th {
            background-color: #f8f9fb;
            color: #31333F;
            font-weight: 600;
            border-bottom: 1px solid rgba(49, 51, 63, 0.15);
            padding: 10px 14px;
            white-space: nowrap;
        }

        .st-table tbody td {
            color: #31333F;
            border-color: rgba(49, 51, 63, 0.1);
            padding: 9px 14px;
            vertical-align: middle;
        }

        .st-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badges */
        .st-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .st-badge-success {
            background-color: rgba(0, 204, 150, 0.16);
            color: #008761;
            border: 1px solid rgba(0, 204, 150, 0.3);
        }

        .st-badge-danger {
            background-color: rgba(239, 85, 59, 0.16);
            color: #b91c1c;
            border: 1px solid rgba(239, 85, 59, 0.3);
        }

        .st-badge-purple {
            background-color: rgba(171, 99, 250, 0.16);
            color: #6d28d9;
            border: 1px solid rgba(171, 99, 250, 0.3);
        }

        .st-badge-warning {
            background-color: rgba(245, 158, 11, 0.16);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .st-badge-secondary {
            background-color: rgba(127, 127, 127, 0.16);
            color: #4b5563;
            border: 1px solid rgba(127, 127, 127, 0.3);
        }

        /* DataTables adjustments */
        .dataTables_wrapper {
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 10px;
            border-radius: 6px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            border: 1px solid #d1d5db;
            padding: 4px 8px;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div class="st-dashboard">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="st-page-title mb-0">Dashboard Monitoring</h1>
        </div>

        @include('components.monitoring.upload-section')

        @include('components.monitoring.dropdown-section')

        @include('components.monitoring.statistic-section')

        @include('components.monitoring.table-section')

    </div>
@endsection

@push('js')
    <script type="module" src="{{ asset('js/sap-dataset/index.js') }}"></script>
    <script src="{{ asset('js/bom-import.js') }}"></script>
@endpush
