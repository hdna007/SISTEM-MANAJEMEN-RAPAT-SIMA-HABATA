<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<div class="wrapper">
    <!-- LEFT -->
    <div class="left">

        <h1>PLEASE SIGN IN</h1>
        <p>Selamat datang di SIMA HABATA. Rapat cerdas dimulai di sini.</p>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('loginError'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('loginError') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Superadmin.id" required autofocus @error('username') value="{{ old('username') }}" @enderror autocomplete="off">
                <div class="invalid-feedback">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Input Password" required autofocus @error('password') @enderror autocomplete="off">
                <div class="invalid-feedback">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn">
                Login
            </button>
        </form>
        <p class="text-center mt-4 register-text">
            Belum punya akun?
            <a href="/register" class="register-link">Daftar sekarang</a>
        </p>

    </div>

    <!-- RIGHT -->
    <div class="right">
        <img src="{{ asset('assets/img/bg.png') }}" alt="Meeting Illustration">
        <p>
            Make your meeting easier and organized with<br>
            <b>SIMA HABATA</b>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
