@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<section class="panel auth-panel">
    <h1 class="page-heading">Buat akun</h1>
    <p class="muted">Daftar untuk mulai membeli produk.</p>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="form-field">
            <label for="nama_lengkap">Nama lengkap</label>
            <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
        </div>
        <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="form-field">
            <label for="username">Username</label>
            <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" required>
        </div>
        <div class="form-field">
            <label for="no_hp">Nomor telepon</label>
            <input id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
        </div>
        <div class="form-field">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" required>{{ old('alamat') }}</textarea>
        </div>
        <div class="form-field">
            <label for="password">Password (minimal 8 karakter)</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
        </div>
        <div class="form-field">
            <label for="password_confirmation">Ulangi password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>
        <button class="button" type="submit">Daftar</button>
    </form>
</section>
@endsection
