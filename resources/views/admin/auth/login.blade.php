<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }

        .login-card {
            max-width: 420px;
            margin: auto;
            margin-top: 10vh;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="login-card">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h4 class="text-center mb-4 fw-bold">
                        Admin Panel
                    </h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="admin@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <button class="btn btn-primary w-100">
                            Login
                        </button>
                    </form>

                </div>
            </div>

            <p class="text-center text-muted mt-3 small">
                © {{ date('Y') }} Sistem Pelaporan
            </p>
        </div>
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Contoh kecil UX improvement
        $('form').on('submit', function() {
            $('button').prop('disabled', true).text('Memproses...');
        });
    </script>

</body>

</html>
