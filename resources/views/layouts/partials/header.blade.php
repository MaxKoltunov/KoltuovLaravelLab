<header class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container header__content">
            <a class="navbar-brand header__brand" href="{{ route('turtles.index') }}">
                <div class="logo">
                    <img src="{{ asset('storage/images/logo.jpg') }}"
                         alt="Логотип" class="logo__img img-fluid">
                </div>
                <span class="site-name">Портал для зоологов</span>
            </a>

            <div class="d-flex flex-wrap align-items-center gap-2">
                @if (($showCatalogActions ?? false) && auth()->check())
                    <a href="{{ route('turtles.create') }}"
                       class="btn-login text-decoration-none">Добавить</a>
                    <button class="btn-login" type="button" id="uploadButton">
                        Загрузить
                    </button>
                @endif

                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                @else
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Пользователи</a>
                    <a href="{{ route('users.turtles', auth()->user()) }}" class="btn btn-outline-primary">Мои черепахи</a>
                    <span class="text-muted">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">Выйти</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>
</header>
