<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

<div class="wrapper">

    <!-- LEFT -->
    <div class="left">

        <h1>Buat akun</h1>
        <p>Gabung dengan SIMA HABATA dan kelola rapat dengan lebih cerdas.</p>

        <form method="POST" action="/register">
            @csrf

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" placeholder="John Doe" required autofocus value="{{ old('nama_lengkap') }}">
                <div class="invalid-feedback">
                    @error('nama_lengkap')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="johndoe123" required value="{{ old('username') }}">
                <div class="invalid-feedback">
                    @error('username')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="gmail">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="johndoe@example.com" required value="{{ old('email') }}">
                <div class="invalid-feedback">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Create password" required>
                <div class="invalid-feedback">
                    @error('password')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Repeat password" required>
                <div class="invalid-feedback">
                    @error('password_confirmation')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn">
                Register
            </button>

        </form>

        <p class="text-center mt-4 register-text">
            Sudah punya akun?
            <a href="/login" class="register-link">Login sekarang</a>
        </p>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <img src="{{ asset('assets/img/bg.png') }}" alt="Meeting Illustration">
        <p>
            Start organizing your meetings with<br>
            <b>SIMA HABATA</b>
        </p>
    </div>

</div>