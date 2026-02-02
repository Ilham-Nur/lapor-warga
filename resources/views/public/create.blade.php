<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lapor Kejadian - Lapor Warga</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Google reCAPTCHA --}}
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>


    <style>
        body {
            background-color: #f8f9fa;
        }

        #map {
            height: 45vh;
            border-radius: 10px;
        }

        .hint {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .optional-box {
            background: #f1f3f5;
            border-radius: 8px;
            padding: 12px;
            display: none;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-danger">
        <div class="container-fluid">
            <a href="{{ route('public.map') }}" class="navbar-brand">
                ← Lapor Warga
            </a>
        </div>
    </nav>

    <div class="container my-3">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- VALIDATION ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">

                <h5 class="mb-3">📢 Laporkan Kejadian</h5>

                <form method="POST" action="{{ route('public.report.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Jenis Kejadian --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Kejadian</label>
                        <select name="report_type_id" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('report_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Waktu Kejadian --}}
                    <div class="mb-3">
                        <label class="form-label">Waktu Kejadian</label>
                        <input type="datetime-local" name="occurred_at" class="form-control"
                            value="{{ old('occurred_at') }}" required>
                    </div>

                    {{-- MAP --}}
                    <div class="mb-2">
                        <label class="form-label">Lokasi Kejadian</label>
                        <div id="map"></div>
                        <div class="hint mt-1">
                            Ketuk / klik pada peta untuk menandai lokasi kejadian.
                        </div>
                    </div>

                    {{-- LAT LNG --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="text" name="latitude" id="latitude" class="form-control"
                                placeholder="Latitude" value="{{ old('latitude') }}" readonly required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="longitude" id="longitude" class="form-control"
                                placeholder="Longitude" value="{{ old('longitude') }}" readonly required>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan Lokasi (opsional)</label>
                        <input type="text" name="address_text" class="form-control"
                            placeholder="Contoh: depan minimarket, dekat lampu merah"
                            value="{{ old('address_text') }}">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kejadian (opsional)</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Ceritakan singkat kejadian yang terjadi">{{ old('description') }}</textarea>
                    </div>

                    {{-- PERTANYAAN BUKTI --}}
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="hasEvidence">
                        <label class="form-check-label" for="hasEvidence">
                            Apakah Anda memiliki bukti pendukung (foto) terkait kejadian ini?
                        </label>
                    </div>

                    {{-- UPLOAD FOTO --}}
                    <div class="optional-box mb-3" id="mediaBox">
                        <label class="form-label">Upload Foto (Opsional)</label>
                        <input type="file" name="media[]" class="form-control" accept="image/*" multiple>
                        <div class="hint mt-1">
                            Maksimal 3 foto, ukuran maksimal 2MB per foto.
                        </div>
                    </div>

                    {{-- CAPTCHA --}}
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}">
                        </div>

                        @error('g-recaptcha-response')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                    <div class="d-grid">
                        <button id="submitBtn" type="submit" class="btn btn-danger btn-lg">
                            Kirim Laporan
                        </button>
                    </div>


                    <div class="hint text-center mt-3">
                        Identitas pelapor <strong>tidak dicatat</strong>.
                        Laporan akan diverifikasi sebelum ditampilkan.
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Leaflet --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Default map Makassar
        const map = L.map('map').setView([-5.147665, 119.432732], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker;

        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(8);
            const lng = e.latlng.lng.toFixed(8);

            $('#latitude').val(lat);
            $('#longitude').val(lng);

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });

        // Toggle upload foto
        $('#hasEvidence').on('change', function() {
            if ($(this).is(':checked')) {
                $('#mediaBox').slideDown();
            } else {
                $('#mediaBox').slideUp();
                $('input[name="media[]"]').val('');
            }
        });


        $('form').on('submit', function() {
            const btn = $('#submitBtn');

            // disable tombol
            btn.prop('disabled', true);

            // ganti tampilan
            btn.html(`
            <span class="spinner-border spinner-border-sm me-2"></span>
            Sedang diproses...
        `);
        });
    </script>

</body>

</html>
