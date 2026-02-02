<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin - Lapor Warga')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            background-color: #f4f6f9;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #212529;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 16px;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background: #dc3545;
            color: #fff;
        }

        .content {
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="d-flex">

        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        <div class="flex-grow-1">

            {{-- Navbar --}}
            @include('admin.partials.navbar')

            {{-- Content --}}
            <main class="content">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    @stack('scripts')

</body>

</html>
