@extends('layouts.app')

@section('title', 'Добавление черепахи — Портал для зоологов')

@section('content')
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
@endsection
