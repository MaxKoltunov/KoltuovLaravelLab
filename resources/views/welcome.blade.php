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

                    <a class="navbar-brand header__brand" href="#">
                        <div class="logo">
                            <img src="{{ asset('storage/images/logo.jpg') }}"
                                alt="Лого"
                                class="logo__img img-fluid">
                        </div>

                        <span class="site-name">
                            Портал для зоологов
                        </span>
                    </a>

                    <button class="btn-login"
                            type="button"
                            id="uploadButton">
                        Загрузить
                    </button>

                </div>
            </nav>
        </header>

        <main class="container">
            <div class="alert alert-info mt-3">
                Черепах в базе данных: {{ $turtles->count() }}
            </div>
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


            
            @php
                // Временная связь с модальными окнами старой лабораторной
                $modalIds = [
                    'Chelonia mydas' => 'greenTurtleModal',
                    'Dermochelys coriacea' => 'leatherbackSeaTurtleModal',
                    'Chelydra serpentina' => 'commonSnappingTurtleModal',
                    'Pyxis arachnoides' => 'pyxisArachnoidesTurtleModal',
                    'Aldabrachelys gigantea' => 'giantTortoiseModal',
                ];
            @endphp

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

                    @php
                        $modalId = $modalIds[$turtle->scientific_name] ?? null;
                    @endphp

                    <div class="col">
                        <article class="card h-100">

                            <div class="card__image-wrapper">

                                <span class="card__badge">
                                    {{ $turtle->category }}
                                </span>

                                <img
                                    src="{{ asset('storage/' . $turtle->image_path) }}"
                                    alt="{{ $turtle->name }}"
                                    class="card-img-top card__img img-fluid">

                            </div>

                            <div class="card-body card__content">

                                <h3 class="card-title card__title">
                                    {{ $turtle->name }}
                                </h3>

                                <p class="card__latin">
                                    {{ $turtle->scientific_name }}
                                </p>

                                <p class="card-text card__desc">
                                    {{ $turtle->short_description }}
                                </p>

                                @if ($modalId)
                                    <button
                                        type="button"
                                        class="btn btn-primary mt-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#{{ $modalId }}">
                                        Подробнее
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        class="btn btn-secondary mt-auto"
                                        disabled>
                                        Подробнее
                                    </button>
                                @endif

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

        <div class="modal fade"
            id="greenTurtleModal"
            tabindex="-1"
            aria-labelledby="greenTurtleModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h2 class="modal-title fs-5"
                            id="greenTurtleModalLabel">
                            Зелёная черепаха
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Закрыть">
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="modal__article">
                            <div class="modal__image-wrapper">
                                <img src="{{ asset('storage/images/Sea-turtle.jpg') }}"
                                    alt="Зелёная черепаха"
                                    title="Зелёная черепаха"
                                    class="img-fluid rounded">
                            </div>

                            <div class="modal__intro">
                                <h3>Chelonia mydas</h3>

                                <p>
                                    Зелёная черепаха — крупная морская черепаха,
                                    обитающая в тропических и
                                    <button type="button"
                                        class="btn btn-link p-0 align-baseline"
                                        data-bs-toggle="popover"
                                        data-bs-title="Субтропические воды"
                                        data-bs-content="Воды в субтропических климатических зонах, расположенных между тропическими и умеренными широтами.">
                                        субтропических водах
                                    </button>
                                    Мирового океана.
                                </p>
                            </div>
                        </div>

                        <p>
                            Взрослые особи питаются преимущественно морскими
                            травами и водорослями. Зелёные черепахи могут
                            совершать длительные миграции между местами
                            кормления и размножения.
                        </p>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade"
            id="leatherbackSeaTurtleModal"
            tabindex="-1"
            aria-labelledby="leatherbackSeaTurtleModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h2 class="modal-title fs-5"
                            id="leatherbackSeaTurtleModalLabel">
                            Кожистая черепаха
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Закрыть">
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="modal__article">
                            <div class="modal__image-wrapper">
                                <img src="{{ asset('storage/images/Leatherback_sea_turtle.jpg') }}"
                                    alt="Кожистая черепаха"
                                    title="Кожистая черепаха"
                                    class="img-fluid rounded mb-3">
                            </div>

                            <div class="modal__intro">
                                <h3>Dermochelys coriacea</h3>

                                <p>
                                    Кожистая черепаха - единственный современный вид из семейства кожистых черепах (Dermochelyidae)
                                </p>
                            </div>
                        </div>

                        <p>
                            Со времён триаса эволюция этих животных шла по отдельному пути,
                            поэтому они значительно отличаются от других черепах.
                            Панцирь их не связан со скелетом и состоит из маленьких костных
                            пластинок, соединённых между собой, наиболее крупные из которых
                            формируют продольные гребни.

                            <button type="button"
                                class="btn btn-link p-0 align-baseline"
                                data-bs-toggle="popover"
                                data-bs-title="Роговые щитки"
                                data-bs-content="Твёрдые наружные пластины, покрывающие панцирь большинства видов черепах.">
                                Роговых щитков
                            </button>

                            нет, панцирь покрыт плотной кожей, возможно, образованной
                            сросшимися роговыми щитками. Средняя плотность тела примерно
                            совпадает с плотностью морской воды.
                        </p>


                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade"
            id="commonSnappingTurtleModal"
            tabindex="-1"
            aria-labelledby="commonSnappingModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h2 class="modal-title fs-5"
                            id="commonSnappingModalLabel">
                            Каймановая черепаха
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Закрыть">
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="modal__article">
                            <div class="modal__image-wrapper">
                                <img src="{{ asset('storage/images/Common_Snapping_Turtle.jpg') }}"
                                    alt="Каймановая черепаха"
                                    title="Каймановая черепаха"
                                    class="img-fluid rounded mb-3">
                            </div>

                            <div class="modal__intro">
                                <h3>Chelydra serpentina</h3>

                                <p>
                                    Каймановая черепаха или кусающаяся черепаха - вид черепах рода Chelydra.
                                </p>
                            </div>
                        </div>

                        <p>
                            Крупное животное, с

                            <button type="button"
                                class="btn btn-link p-0 align-baseline"
                                data-bs-toggle="popover"
                                data-bs-title="Панцирь"
                                data-bs-content="Защитное образование, покрывающее тело черепахи и состоящее из спинного и брюшного щитов.">
                                панцирем
                            </button>

                            длиной до 35 см, массой до 14 кг. Отдельные особи массой
                            до 30 кг. Каймановая черепаха имеет большую голову с
                            выпуклыми глазами, большую пасть с острыми челюстями,
                            мощные когтистые лапы. Животное известно своей
                            агрессивностью — при поимке черепаха активно обороняется,
                            далеко выбрасывая голову на длинной шее и кусаясь.
                        </p>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade"
            id="pyxisArachnoidesTurtleModal"
            tabindex="-1"
            aria-labelledby="pyxisArachnoidesModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h2 class="modal-title fs-5"
                            id="pyxisArachnoidesModalLabel">
                            Паучья черепаха
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Закрыть">
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="modal__article">
                            <div class="modal__image-wrapper">
                                <img src="{{ asset('storage/images/Pyxis_Arachnoides_Ste_Marie_Madagascar.jpg') }}"
                                    alt="Паучья черепаха"
                                    title="Паучья черепаха"
                                    class="img-fluid rounded mb-3">
                            </div>

                            <div class="modal__intro">
                                <h3>Pyxis arachnoides</h3>

                                <p>
                                    Паучья черепаха - вид сухопутных черепах. Эндемик Мадагаскара. Малоизученный вид, его численность сокращается
                                </p>
                            </div>
                        </div>

                        <p>
                            Мелкая черепаха, длина её панциря до 15 см.
                            Он высокий, округлый. Передняя доля

                            <button type="button"
                                class="btn btn-link p-0 align-baseline"
                                data-bs-toggle="popover"
                                data-bs-title="Пластрон"
                                data-bs-content="Брюшной щит панциря черепахи, защищающий тело животного снизу.">
                                пластрона
                            </button>

                            подвижно соединена с основной его частью поперечной
                            сухожильной связкой. Это позволяет черепахе в случае
                            опасности плотно закрываться спереди. Панцирь
                            тёмно-коричневый, покрытый ажурным жёлтым рисунком,
                            похожим на паутину. За этот рисунок паучья черепаха
                            и получила своё название. Голова чёрная, покрытая
                            маленькими светлыми пятнами. Лапы снаружи жёлтые,
                            а на внутренних поверхностях чёрные.
                        </p>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade"
            id="giantTortoiseModal"
            tabindex="-1"
            aria-labelledby="giantTortoiseModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h2 class="modal-title fs-5"
                            id="giantTortoiseModalLabel">
                            Гигантская черепаха
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Закрыть">
                        </button>
                    </div>
                    
                    <div class="modal-body">

                        <div class="modal__article">
                            <div class="modal__image-wrapper">
                                <img src="{{ asset('storage/images/Giant_Tortoise.JPG') }}"
                                    alt="Гигантская черепаха"
                                    class="img-fluid rounded mb-3">
                            </div>

                            <div class="modal__intro">
                                <h3>Aldabrachelys gigantea</h3>

                                <p>
                                    Гигантская черепаха - вид сухопутных черепах. Эндемик острова Альдабра, редкий вид.
                                </p>
                            </div>
                        </div>

                        <p>
                            Очень крупная черепаха с мощными столбообразными ногами
                            и относительно маленькой головой. Длина

                            <button type="button"
                                class="btn btn-link p-0 align-baseline"
                                data-bs-toggle="popover"
                                data-bs-title="Карапакс"
                                data-bs-content="Спинной, то есть верхний, щит панциря черепахи.">
                                карапакса
                            </button>

                            изредка достигает 120 см, но обычно не более 105.
                            Один из двух крупнейших видов наземных черепах.
                            На острове Альдабра особи из различных популяций
                            отличаются размерами и формой панциря. Некоторые
                            специалисты рассматривают эти популяции как подвиды.
                        </p>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>

                </div>
            </div>
        </div>

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