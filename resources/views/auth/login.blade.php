<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body style="background-color: #f8f9fc;">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card border-0 shadow-lg p-4" style="width: 400px; border-radius: 15px;">
            <div class="text-center mb-4">
                <i class="bi bi-fingerprint text-primary" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mt-2 text-dark">Admin Login</h4>
                <p class="text-muted small">Silakan masuk untuk mengelola absensi</p>
            </div>
            
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm py-2 small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nip" class="form-label fw-medium small text-uppercase text-muted">NIP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="nip" class="form-control border-start-0 bg-light" placeholder="Masukkan NIP" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-medium small text-uppercase text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 bg-light" placeholder="Masukkan Password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-bold">Masuk</button>
            </form>
            
            <div class="text-center mt-3">
                <small class="text-muted" style="font-size: 0.7rem;">Backend API: {{ env('API_BASE_URL') }}</small>
            </div>
        </div>
    </div>
</body>
</html>