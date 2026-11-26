@extends('layouts.app', ['showCatalogActions' => true])

@section('title', $turtle->name . ' — Портал для зоологов')

@section('content')
    @php
        $glossary = config('turtle_glossary')[$turtle->scientific_name] ?? null;
    @endphp

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
@endsection
