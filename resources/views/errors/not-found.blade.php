@extends('layouts.app')

@section('auth')
    @include('layouts.navbars.auth.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        @include('layouts.navbars.auth.nav')
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="display-4 text-warning">404</h1>
                    <h4 class="mb-3">Halaman Tidak Ditemukan</h4>
                    <p class="text-secondary">Sepertinya halaman yang kamu cari nggak tersedia atau sudah dipindahkan.</p>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-primary mt-3">Kembali ke halaman sebelumnya</a>
                </div>
            </div>
        </div>
    </main>
@endsection
