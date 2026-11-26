@extends('layouts.app')

@section('title', 'Вход — Портал для зоологов')

@section('content')
    <section class="family-info mx-auto" style="max-width: 480px;">
        <h1 class="family-title">Вход</h1>
        <div class="card family-content">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Электронная почта</label>
                        <input id="email" name="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" autocomplete="email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input id="password" name="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="current-password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check mb-3">
                        <input id="remember" type="checkbox" name="remember" value="1"
                               class="form-check-input" @checked(old('remember'))>
                        <label for="remember" class="form-check-label">Запомнить меня</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Войти</button>
                    <a href="{{ route('register') }}" class="btn btn-link">Регистрация</a>
                </form>
            </div>
        </div>
    </section>
@endsection
