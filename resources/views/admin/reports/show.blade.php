@extends('admin.layouts.app')

@section('title', 'Detail Laporan')

@section('content')
    <h4 class="mb-3">📄 Detail Laporan #{{ $report->id }}</h4>

    <div class="row">
        {{-- INFO --}}
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Jenis Kejadian</th>
                            <td>{{ $report->type->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Kejadian</th>
                            <td>
                                {{ $report->occurred_at }}
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span
                                    class="badge bg-{{ $report->status === 'pending' ? 'warning' : ($report->status === 'verified' ? 'success' : 'danger') }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>
                                {{ $report->address_text ?? '-' }} <br>
                                <small class="text-muted">
                                    {{ $report->latitude }}, {{ $report->longitude }}
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $report->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Kejadian</th>
                            <td>
                                <div id="map" style="height: 400px;"></div>
                                <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                                    target="_blank" class="btn btn-outline-primary btn-sm">
                                    🧭 Buka di Google Maps
                                </a>
                            </td>
                        </tr>
                    </table>

                    {{-- ACTION --}}
                    @if ($report->status === 'pending')
                        <form method="POST" action="{{ route('admin.reports.approve', $report->id) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Approve</button>
                        </form>

                        <form method="POST" action="{{ route('admin.reports.reject', $report->id) }}"
                            class="d-inline ms-1">
                            @csrf
                            <button class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- FOTO --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Bukti Foto</h6>

                    @if ($report->media->count())
                        <div class="row g-2">
                            @foreach ($report->media as $media)
                                <div class="col-6">
                                    <a href="{{ asset('storage/' . $media->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $media->file_path) }}"
                                            class="img-fluid rounded border">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Tidak ada foto bukti.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="card shadow-sm mt-3">
        <div class="card-header fw-semibold">
            📍 Lokasi Kejadian
        </div>
        <div class="card-body p-0">
            <div id="map" style="height: 400px;"></div>
        </div>
        <div class="card-footer text-end">
            <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank"
                class="btn btn-outline-primary btn-sm">
                🧭 Buka di Google Maps
            </a>
        </div>
    </div> --}}

    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary mt-3">
        ← Kembali
    </a>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const lat = {{ $report->latitude }};
            const lng = {{ $report->longitude }};

            if (!lat || !lng) {
                document.getElementById('map').innerHTML =
                    '<p class="text-center text-muted p-3">Lokasi tidak tersedia</p>';
                return;
            }

            const map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup(`
                <strong>{{ $report->type->name ?? 'Laporan Warga' }}</strong><br>
                {{ $report->address_text ?? 'Koordinat GPS' }}
            `)
                .openPopup();
        });
    </script>
@endpush
