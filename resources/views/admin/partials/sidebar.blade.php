<div class="sidebar">
    <div class="p-3 text-white fw-bold border-bottom">
        PANEL ADMIN
    </div>

    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        📊 Dashboard
    </a>

    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        📄 Laporan Warga
    </a>

    <a href="#">
        🗂 Jenis Kejadian
    </a>

    <a href="#">
        👤 User Admin
    </a>

    <a href="#">
        ⚙️ Pengaturan
    </a>
</div>
