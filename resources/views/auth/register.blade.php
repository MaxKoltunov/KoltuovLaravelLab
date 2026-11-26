@extends('layouts.app')

@section('title', 'Регистрация — Портал для зоологов')

@section('content')
    <section class="family-info mx-auto" style="max-width: 480px;">
        <h1 class="family-title">Регистрация</h1>
        <div class="card family-content">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input id="name" name="name" type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" autocomplete="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Имя пользователя (username)</label>
                        <input id="username" name="username" type="text"
                               class="form-control @error('username') is-invalid @enderror"
                               value="{{ old('username') }}" autocomplete="username" required
                               minlength="3" maxlength="32" pattern="[a-z][a-z0-9_]{2,31}"
                               aria-describedby="username-help">
                        <div id="username-help" class="form-text">
                            От 3 до 32 символов: строчные латинские буквы, цифры и подчёркивания.
                            Первый символ — буква. Используется в ссылке на ваш каталог.
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Электронная почта</label>
                        <input id="email" name="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" autocomplete="email" required
                               pattern=".+@.+[.].+" title="Например, name@gmail.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input id="password" name="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Повторите пароль</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               autocomplete="new-password" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    <a href="{{ route('login') }}" class="btn btn-link">Уже есть аккаунт</a>
                </form>
            </div>
        </div>
    </section>
@endsection
