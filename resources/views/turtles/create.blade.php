<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Добавление черепахи — Портал для зоологов</title>
        <link rel="stylesheet" href="{{ mix('css/styles.css') }}">
    </head>
    <body>
        <header class="header">
            <nav class="navbar navbar-expand-lg">
                <div class="container header__content">
                    <a href="{{ route('turtles.index') }}"
                       class="navbar-brand header__brand">
                        <div class="logo">
                            <img src="{{ asset('storage/images/logo.jpg') }}"
                                 alt="Логотип" class="logo__img img-fluid">
                        </div>
                        <span class="site-name">Портал для зоологов</span>
                    </a>
                </div>
            </nav>
        </header>

        <main class="container">
            <section class="family-info">
                <a href="{{ route('turtles.index') }}"
                   class="d-inline-block mb-3 text-decoration-none">
                    &larr; Вернуться к каталогу
                </a>

                <h1 class="family-title">Добавить черепаху</h1>

                <div class="card family-content">
                    <div class="card-body p-4">
                        <p class="text-muted">Поля со звёздочкой обязательны для заполнения.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                Проверьте поля формы. Некоторые значения не прошли проверку.
                            </div>
                        @endif

                        <form action="{{ route('turtles.store') }}"
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            @include('turtles.partials.form', ['turtle' => null])

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Добавить черепаху
                                </button>
                                <a href="{{ route('turtles.index') }}"
                                   class="btn btn-outline-secondary">Отмена</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="container footer__content">
                <div class="footer__author">Колтунов Максим Витальевич</div>
            </div>
        </footer>

        <script src="{{ mix('js/bundle.js') }}"></script>
    </body>
</html>
