<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $turtle->name }} — Портал для зоологов</title>
        <link rel="stylesheet" href="{{ mix('css/styles.css') }}">
    </head>
    <body>
        <header class="header">
            <nav class="navbar navbar-expand-lg">
                <div class="container header__content">
                    <a class="navbar-brand header__brand"
                       href="{{ route('turtles.index') }}">
                        <div class="logo">
                            <img src="{{ asset('storage/images/logo.jpg') }}"
                                 alt="Логотип"
                                 class="logo__img img-fluid">
                        </div>
                        <span class="site-name">Портал для зоологов</span>
                    </a>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('turtles.create') }}"
                           class="btn-login text-decoration-none">
                            Добавить
                        </a>
                        <button class="btn-login" type="button" id="uploadButton">
                                                Загрузить
                                            </button>
                    </div>
                </div>
            </nav>
        </header>

        @php
            $glossary = config('turtle_glossary')[$turtle->scientific_name] ?? null;
        @endphp

        <main class="container">
            @if (session('success'))
                <div class="alert alert-success mt-3" role="status">
                    {{ session('success') }}
                </div>
            @endif
            <section class="family-info">
                <a href="{{ route('turtles.index') }}"
                   class="d-inline-block mb-3 text-decoration-none">
                    &larr; Вернуться к каталогу
                </a>

                <h1 class="family-title">
                    {{ $turtle->name }}
                    <span class="latin">({{ $turtle->scientific_name }})</span>
                </h1>

                <div class="card family-content">
                    <div class="card-body p-4">
                        <div class="modal__article">
                            <div class="modal__image-wrapper">
                                @if ($turtle->image_path)
                                    <img src="{{ asset('storage/' . $turtle->image_path) }}"
                                         alt="{{ $turtle->name }}"
                                         title="{{ $turtle->name }}"
                                         class="img-fluid rounded">
                                @else
                                    <div class="alert alert-secondary">
                                        Изображение пока не загружено.
                                    </div>
                                @endif
                            </div>

                            <div class="modal__intro">
                                <span class="badge text-bg-info mb-3">
                                    {{ $turtle->category }}
                                </span>

                                <h2 class="fs-5">О виде</h2>
                                <p>@include('turtles.partials.glossary-text', [
                                    'content' => $turtle->intro_description,
                                    'field' => 'intro_description',
                                    'glossary' => $glossary,
                                ])</p>

                                @if ($turtle->catalogued_at)
                                    <p class="text-muted small">
                                        Добавлено в каталог:
                                        {{ $turtle->catalogued_at->format('d.m.Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <h2 class="fs-5">Подробное описание</h2>
                        <p style="white-space: pre-line;">@include('turtles.partials.glossary-text', [
                            'content' => $turtle->full_description,
                            'field' => 'full_description',
                            'glossary' => $glossary,
                        ])</p>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            <a href="{{ route('turtles.index') }}"
                               class="btn btn-primary">
                                К списку черепах
                            </a>

                            <a href="{{ route('turtles.edit', $turtle) }}"
                               class="btn btn-outline-primary">
                                Редактировать
                            </a>

                            <form action="{{ route('turtles.destroy', $turtle) }}"
                                  method="POST"
                                  onsubmit="return confirm('Вы действительно хотите удалить эту черепаху из каталога?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    Удалить черепаху
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="container footer__content">
                <div class="footer__author">Колтунов Максим Витальевич</div>
            </div>
        </footer>

        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="uploadToast" class="toast" role="alert"
                 aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="fa-solid fa-arrows-rotate fa-spin me-2"
                       aria-hidden="true"></i>
                    <strong class="me-auto">Портал для зоологов</strong>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="toast" aria-label="Закрыть"></button>
                </div>
                <div class="toast-body">
                    На текущий момент функционал загрузки недоступен.
                </div>
            </div>
        </div>

        <script src="{{ mix('js/bundle.js') }}"></script>
    </body>
</html>
