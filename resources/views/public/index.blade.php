@php
    use Carbon\Carbon;

    $defaultFrom = request('from') ?? Carbon::now()->subMonths(3)->format('Y-m-d');

    $defaultTo = request('to') ?? Carbon::now()->format('Y-m-d');
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lapor Warga - Peta Kejadian</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        #map {
            height: 65vh;
            width: 100%;
            border-radius: 10px;
        }

        .filter-box {
            background: #fff;
            border-radius: 10px;
            padding: 12px;
        }

        .legend-box {
            background: #fff;
            border-radius: 10px;
            padding: 12px;
            margin-top: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-right: 8px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-danger">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">🚨 Lapor Warga</span>
            <a href="{{ route('public.report.create') }}" class="btn btn-light btn-sm">
                + Lapor Kejadian
            </a>
        </div>
    </nav>

    <div class="container my-3">

        {{-- FILTER --}}
        <div class="filter-box mb-3">
            <form method="GET" action="{{ route('public.map') }}" class="row g-2">

                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">
                        Tanggal Mulai
                    </label>
                    <input type="date" name="from" class="form-control" value="{{ $defaultFrom }}">
                    {{-- <small class="text-muted">
                        Mulai dari tanggal kejadian
                    </small> --}}
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">
                        Tanggal Akhir
                    </label>
                    <input type="date" name="to" class="form-control" value="{{ $defaultTo }}">
                    {{-- <small class="text-muted">
                        Sampai dengan tanggal kejadian
                    </small> --}}
                </div>

                <div class="col-12 col-md-4">
                    <select name="type" class="form-select">
                        <option value="">Semua Jenis Kejadian</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-6 col-md-1 d-grid">
                    <button class="btn btn-danger">Filter</button>
                </div>

                <div class="col-6 col-md-1 d-grid">
                    <a href="{{ route('public.map') }}"
                        class="btn btn-outline-secondary
                            {{ request()->hasAny(['type', 'from', 'to']) ? '' : 'disabled' }}">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- RINGKASAN & LEGENDA --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>📊 Ringkasan Laporan</strong>
                    <span class="badge bg-danger">
                        {{ $summary['total'] }} Total
                    </span>
                </div>

                <div class="row g-2">
                    @foreach ($types as $type)
                        <div class="col-6 col-md-4">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <span class="legend-color me-2"
                                    style="background: {{ $type->color ?? '#dc3545' }}"></span>

                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="font-size: 0.9rem">
                                        {{ $type->name }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $summary['by_type'][$type->id] ?? 0 }} laporan
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-muted small mt-2">
                    Warna pada peta menunjukkan jenis kejadian.
                </div>

            </div>
        </div>


        {{-- MAP --}}
        <div id="map"></div>

        {{-- INFO --}}
        <div class="mt-3 text-muted small">
            Menampilkan <strong>{{ $reports->count() }}</strong> laporan yang telah diverifikasi.
        </div>

    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Leaflet Heatmap JS --}}
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <script>
        const map = L.map('map', {
            preferCanvas: true // 🔥 performa naik
        }).setView([-5.147665, 119.432732], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // DATA
        const reports = @json($reports);
        const heatData = @json($heatmapData);

        /**
         * =====================================================
         * 📍 MARKER CLUSTER (OPTIMIZED)
         * =====================================================
         */
        const markerCluster = L.markerClusterGroup({
            chunkedLoading: true, // 🔥 anti freeze
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            maxClusterRadius: 50
        });

        reports.forEach(report => {
            if (!report.latitude || !report.longitude) return;

            const marker = L.circleMarker(
                [report.latitude, report.longitude], {
                    radius: 7,
                    color: report.type?.color ?? '#dc3545',
                    fillOpacity: 0.85
                }
            );

            // ⚡ POPUP RINGAN (NO DESCRIPTION)
            marker.bindPopup(`
            <strong>${report.type?.name ?? 'Kejadian'}</strong><br>
            <small>${new Date(report.occurred_at).toLocaleString()}</small>
            <hr>
            <button class="btn btn-sm btn-outline-danger w-100"
                onclick="openDetail(${report.id})">
                🔍 Lihat Detail
            </button>
        `);

            markerCluster.addLayer(marker);
        });

        map.addLayer(markerCluster);

        /**
         * =====================================================
         * 🔥 HEATMAP
         * =====================================================
         */
        const heatLayer = L.heatLayer(heatData, {
            radius: 30,
            blur: 20,
            maxZoom: 16
        });

        /**
         * =====================================================
         * 🧭 LAYER CONTROL
         * =====================================================
         */
        L.control.layers(null, {
            "📍 Marker Kejadian": markerCluster,
            "🔥 Peta Kerawanan": heatLayer
        }, {
            collapsed: false
        }).addTo(map);

        /**
         * =====================================================
         * 🔍 DETAIL HANDLER
         * =====================================================
         */
        function openDetail(id) {
            window.location.href = `/report/${id}`;
        }
    </script>



</body>

</html>
