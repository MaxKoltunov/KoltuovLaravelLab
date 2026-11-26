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

            @if ($showCatalogActions ?? false)
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('turtles.create') }}"
                       class="btn-login text-decoration-none">Добавить</a>
                    <button class="btn-login" type="button" id="uploadButton">
                        Загрузить
                    </button>
                </div>
            @endif
        </div>
    </nav>
</header>
