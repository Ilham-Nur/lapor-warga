@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <h4 class="mb-3">📊 Admin Dashboard</h4>

    {{-- STAT BOX --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h4>{{ $stats['total'] }}</h4>
                    <small class="text-muted">Total Laporan</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $stats['pending'] }}</h4>
                    <small>Pending</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $stats['verified'] }}</h4>
                    <small>Verified</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <h4 class="text-danger">{{ $stats['rejected'] }}</h4>
                    <small>Rejected</small>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="row">

        {{-- LAPORAN TERBARU --}}
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>🕒 Laporan Terbaru</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestReports as $report)
                                <tr>
                                    <td>{{ $report->type->name ?? '-' }}</td>
                                    <td>
                                        {{ $report->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $report->status === 'pending' ? 'warning' : ($report->status === 'verified' ? 'success' : 'danger') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.reports.show', $report->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Belum ada laporan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TOP JENIS --}}
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>🔥 Jenis Kejadian Terbanyak</strong>
                </div>
                <div class="card-body">
                    @foreach ($topTypes as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item->type->name ?? '-' }}</span>
                            <strong>{{ $item->total }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

@endsection
