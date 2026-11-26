@extends('layouts.app')

@section('title', 'Редактирование: ' . $turtle->name . ' — Портал для зоологов')

@section('content')
    <section class="family-info">
        <a href="{{ route('turtles.show', $turtle) }}"
           class="d-inline-block mb-3 text-decoration-none">
            &larr; Вернуться к описанию
        </a>

        <h1 class="family-title">Редактировать черепаху</h1>

        <div class="card family-content">
            <div class="card-body p-4">
                <p class="text-muted">Поля со звёздочкой обязательны для заполнения.</p>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        Проверьте поля формы. Некоторые значения не прошли проверку.
                    </div>
                @endif

                <form action="{{ route('turtles.update', $turtle) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Переиспользуем поля из формы создания. --}}
                    @include('turtles.partials.form', ['turtle' => $turtle])

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            Сохранить изменения
                        </button>
                        <a href="{{ route('turtles.show', $turtle) }}"
                           class="btn btn-outline-secondary">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
