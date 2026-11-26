@extends('layouts.app')

@section('title', 'Пользователи — Портал для зоологов')

@section('content')
    <section class="family-info">
        <h1 class="family-title">Пользователи каталога</h1>
        <div class="card family-content">
            <div class="card-body p-4">
                <div class="list-group">
                    @forelse ($users as $user)
                        <a href="{{ route('users.turtles', $user) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>{{ $user->name }} @if ($user->is(auth()->user())) (вы) @endif</span>
                            <span class="badge text-bg-primary rounded-pill">{{ $user->turtles_count }}</span>
                        </a>
                    @empty
                        <p class="text-muted mb-0">Пользователей пока нет.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
