@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<section class="panel auth-panel">
    <h1 class="page-heading">Masuk</h1>
    <p class="muted">Masuk untuk berbelanja dan melihat pesanan Anda.</p>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="form-field">
            <label for="username">Username</label>
            <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" required autofocus>
        </div>
        <div class="form-field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <label style="font-weight: 500; margin-bottom: 18px">
            <input type="checkbox" name="remember" value="1" style="width: auto"> Ingat saya
        </label>
        <button class="button" type="submit">Masuk</button>
    </form>
    <p class="muted" style="margin-bottom: 0">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>.</p>
</section>
@endsection
