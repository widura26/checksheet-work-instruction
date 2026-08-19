@extends('layouts.app')

@section('title', 'Detail Activity Log')

@section('content')
    <div class="container-fluid">
        <div class="row"></div>
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Detail Activity Log #{{ $activity->id }}</h3>
                    <a href="{{ route('activity-log.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi Dasar</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>ID:</strong></td>
                                            <td>{{ $activity->id }}</ng>ID:</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Log Name:</strong></td>
                                            <td><span class="badge badge-primary">{{ $activity->log_name }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Deskripsi:</strong></td>
                                            <td>{{ $activity->description }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Waktu:</strong></td>
                                            <td>
                                                {{ $activity->created_at->format('d/m/Y H:i:s') }}<br>
                                                <small
                                                    class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi User</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>User:</strong></td>
                                            <td>
                                                @if ($activity->causer)
                                                    <span
                                                        class="badge badge-info">{{ $activity->causer->name ?? 'N/A' }}</span>
                                                @else
                                                    <span class="badge badge-secondary">System</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>IP Address:</strong></td>
                                            <td>{{ $activity->properties['ip_address'] ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($activity->properties && $activity->properties->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Properties</h5>
                                    </div>
                                    <div class="card-body">
                                        <pre class="bg-light p-3 rounded"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
