@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activity Log</h3>
                    </div>

                    <!-- Filter Form -->
                    <div class="card-body">
                        <form method="GET" action="{{ route('activity-log.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="search">Pencarian</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                            value="{{ request('search') }}"
                                            placeholder="Cari deskripsi, user, atau model...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="log_name">Log Name</label>
                                        <select class="form-control" id="log_name" name="log_name">
                                            <option value="">Semua</option>
                                            @foreach ($logNames as $logName)
                                                <option value="{{ $logName }}"
                                                    {{ request('log_name') == $logName ? 'selected' : '' }}>
                                                    {{ ucfirst($logName) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_from">Dari Tanggal</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_to">Sampai Tanggal</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                            <a href="{{ route('activity-log.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Activity Log Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="15%">Waktu</th>
                                        <th width="10%">User</th>
                                        <th width="10%">Log Name</th>
                                        <th width="20%">Deskripsi</th>
                                        <th width="15%">Subject</th>
                                        <th width="10%">Event</th>
                                        <th width="10%">IP Address</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td>{{ $activity->id }}</td>
                                            <td>
                                                <small>
                                                    {{ $activity->created_at->format('d/m/Y H:i:s') }}<br>
                                                    <span
                                                        class="text-muted">{{ $activity->created_at->diffForHumans() }}</span>
                                                </small>
                                            </td>
                                            <td>
                                                @if ($activity->causer)
                                                    <span>{{ $activity->causer->name ?? 'N/A' }}</span>
                                                @else
                                                    <span class="badge badge-secondary">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span>{{ $activity->log_name }}</span>
                                            </td>
                                            <td>{{ $activity->description }}</td>
                                            <td>
                                                @if ($activity->subject_type)
                                                    <small>
                                                        {{ class_basename($activity->subject_type) }}
                                                        @if ($activity->subject_id)
                                                            #{{ $activity->subject_id }}
                                                        @endif
                                                    </small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($activity->event)
                                                    @php
                                                        $eventColors = [
                                                            'created' => 'success',
                                                            'updated' => 'warning',
                                                            'deleted' => 'danger',
                                                            'retrieved' => 'info',
                                                        ];
                                                        $color = $eventColors[$activity->event] ?? 'secondary';
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $color }}">{{ $activity->event }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $activity->properties['ip_address'] ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('activity-log.show', $activity) }}"
                                                    class="btn btn-info text-white btn-sm" title="Detail">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Tidak ada activity log yang ditemukan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Menampilkan {{ $activities->firstItem() ?? 0 }} sampai
                                    {{ $activities->lastItem() ?? 0 }}
                                    dari {{ $activities->total() }} hasil
                                </small>
                            </div>
                            <div>
                                {{ $activities->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge {
            font-size: 0.75em;
        }
    </style>
@endpush
