<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Семейство черепах</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ mix('css/styles.css') }}">
    </head>
    <body>
        <header class="header">
            <nav class="navbar navbar-expand-lg">
                <div class="container header__content">

                    <a class="navbar-brand header__brand" href="{{ route('turtles.index') }}">
                        <div class="logo">
                            <img src="{{ asset('storage/images/logo.jpg') }}"
                                alt="Лого"
                                class="logo__img img-fluid">
                        </div>

                        <span class="site-name">
                            Портал для зоологов
                        </span>
                    </a>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('turtles.create') }}"
                           class="btn-login text-decoration-none">
                            Добавить
                        </a>
                        <button class="btn-login"
                                                    type="button"
                                                    id="uploadButton">
                                                Загрузить
                                            </button>
                    </div>

                </div>
            </nav>
        </header>

        <main class="container">

<section class="family-info">
                <h1 class="family-title">
                    Скрытошейные черепахи
                    <span class="latin">(Cryptodira)</span>
                </h1>

                <div class="card family-content">
                    <div class="card-body">
                        <div class="row align-items-start g-4">

                            <div class="col-12 col-md-6">
                                <div class="family-features">
                                    <h2>Описание:</h2>

                                    <p>
                                        Многообразная группа черепах,
                                        включающая пресноводные, морские
                                        и наземные виды.

                                        Скрытошейные черепахи способны
                                        втягивать шею и голову под панцирь,
                                        изгибая шею в вертикальной плоскости.

                                        В связи с этим шейные позвонки
                                        лишены поперечных отростков
                                        (или они рудиментарны).

                                        <br><br>

                                        Распространены по всему жаркому
                                        и умеренному поясу земного шара
                                        кроме Австралии.
                                    </p>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="family-photo-wrapper">
                                    <img src="{{ asset('storage/images/Family-image.jpg') }}"
                                        alt="Исполинская черепаха"
                                        class="family-img img-fluid">

                                    <span class="family-img-caption">
                                        Исполинская черепаха
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>


            
            <div class="row
                        row-cols-1
                        row-cols-sm-1
                        row-cols-md-2
                        row-cols-lg-2
                        row-cols-xl-3
                        row-cols-xxl-3
                        turtle-grid
                        g-4">

                @forelse ($turtles as $turtle)
                    <div class="col">
                        <article class="card h-100">

                            <div class="card__image-wrapper">
                                <span class="card__badge">{{ $turtle->category }}</span>

                                @if ($turtle->image_path)
                                    <img src="{{ asset('storage/' . $turtle->image_path) }}"
                                         alt="{{ $turtle->name }}"
                                         title="{{ $turtle->name }}"
                                         class="card-img-top card__img img-fluid">
                                @endif
                            </div>

                            <div class="card-body card__content">
                                <h3 class="card-title card__title">{{ $turtle->name }}</h3>

                                <p class="card__latin">{{ $turtle->scientific_name }}</p>

                                <p class="card-text card__desc">
                                    {{ $turtle->short_description }}
                                </p>

                                <a href="{{ route('turtles.show', $turtle) }}"
                                   class="btn btn-primary mt-auto">
                                    Подробнее
                                </a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            В каталоге пока нет черепах.
                        </div>
                    </div>
                @endforelse

            </div>
        </main>

        <footer class="footer">
            <div class="container footer__content">
                <div class="footer__author">Колтунов Максим Витальевич</div>
            </div>
        </footer>

        <div class="toast-container position-fixed bottom-0 end-0 p-3">

            <div id="uploadToast"
                class="toast"
                role="alert"
                aria-live="assertive"
                aria-atomic="true">

                <div class="toast-header">

                    <i class="fa-solid fa-arrows-rotate fa-spin me-2"
                    aria-hidden="true"></i>

                    <strong class="me-auto">Портал для зоологов</strong>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="toast"
                            aria-label="Закрыть">
                    </button>

                </div>

                <div class="toast-body">
                    На текущий момент функционал загрузки недоступен.
                </div>

            </div>

        </div>

        <script src="{{ mix('js/bundle.js') }}"></script>
    </body>
</html>