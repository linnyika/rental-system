@extends('layouts.app')

@section('content')
    <div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100"
        style="background: linear-gradient(135deg, #13293d 0%, #1f3b57 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                                    <div class="brand-chip"
                                        style="width: 50px; height: 50px; background: #13293d; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 20px;">
                                        RS
                                    </div>
                                    <div>
                                        <div class="small text-uppercase fw-semibold text-muted">Rental System</div>
                                        <div class="h5 mb-0">@yield('auth-title', 'Welcome')</div>
                                    </div>
                                </div>
                                <p class="text-muted small">@yield('auth-subtitle', 'Sign in to your account')</p>
                            </div>
                            @yield('auth-content')
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} Rental System. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
