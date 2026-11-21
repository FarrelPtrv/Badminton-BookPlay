@extends('layouts.app')

@section('title', 'Masuk Akun - Badminton BookPlay')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle bg-light text-primary mb-2">
                            <i class="bi bi-person-circle fs-1" style="color: var(--primary-color);"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Masuk ke Akun Anda</h4>
                        <p class="text-muted small">Pesan lapangan badminton favoritmu sekarang</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="contoh@gmail.com" required autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label fw-semibold small mb-0">Password</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small text-muted" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                        </button>
                    </form>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="small text-muted mb-0">Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar sekarang</a></p>
                    </div>

                    <!-- Demo Account Helpers -->
                    <div class="mt-4 p-3 bg-light rounded-3 small">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1"></i> Akun Demo Tersedia:</div>
                        <div class="d-flex flex-column gap-1 text-muted" style="font-size: 12px;">
                            <div><strong>Admin:</strong> admin@gorbadminton.com / <code>password</code></div>
                            <div><strong>Customer:</strong> customer@gmail.com / <code>password</code></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
