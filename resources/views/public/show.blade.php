<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Laporan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        #map {
            height: 260px;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-danger">
        <div class="container-fluid">
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">← Kembali</a>
            <span class="navbar-brand">Detail Laporan</span>
        </div>
    </nav>

    <div class="container my-3">

        {{-- INFO UTAMA --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <span class="badge mb-2" style="background-color: {{ $report->type->color }};">
                    {{ $report->type->name }}
                </span>

                <div class="text-muted small mb-2">
                    {{ $report->occurred_at->timezone('Asia/Makassar')->format('d M Y H:i') }}
                </div>

                <p class="mb-0">
                    {{ $report->description ?? 'Tidak ada deskripsi.' }}
                </p>
            </div>
        </div>

        {{-- MAP --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <strong>📍 Lokasi Kejadian</strong>
                <div id="map" class="mt-2"></div>
            </div>
        </div>

        {{-- FOTO --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <strong>📸 Foto Bukti</strong>

                @if ($report->media->count())
                    <div class="row g-2 mt-2">
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
                    <p class="text-muted mt-2">Tidak ada foto.</p>
                @endif
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView(
            [{{ $report->latitude }}, {{ $report->longitude }}],
            15
        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
            .addTo(map);

        L.marker([{{ $report->latitude }}, {{ $report->longitude }}])
            .addTo(map);
    </script>

</body>

</html>
