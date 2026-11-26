@extends('layouts.app', ['showCatalogActions' => true])

@section('title', isset($catalogUser) ? 'Черепахи пользователя ' . $catalogUser->name : 'Семейство черепах')

@section('content')
    @if (isset($catalogUser))
        <section class="family-info">
            <a href="{{ route('users.index') }}" class="d-inline-block mb-3 text-decoration-none">
                &larr; К списку пользователей
            </a>
            <h1 class="family-title">Черепахи пользователя {{ $catalogUser->name }}</h1>
            <a href="{{ route('turtles.index') }}" class="btn btn-outline-primary">Общий каталог</a>
        </section>
    @else
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
    @endif

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

                        @auth
                            <p class="small text-muted">
                                Владелец:
                                @if ($turtle->user)
                                    <a href="{{ route('users.turtles', $turtle->user) }}">{{ $turtle->user->name }}</a>
                                @else
                                    не назначен
                                @endif
                            </p>
                        @endauth

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
@endsection
