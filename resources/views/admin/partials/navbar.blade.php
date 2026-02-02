<nav class="navbar navbar-expand navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">
            🚨 Admin Lapor Warga
        </span>

        <div class="ms-auto">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger btn-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
