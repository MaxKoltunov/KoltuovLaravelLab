@extends('layouts.app')

@section('title', ($friendsOnly ? 'Мои друзья' : 'Пользователи') . ' — Портал для зоологов')

@section('content')
    <section class="family-info">
        <h1 class="family-title">{{ $friendsOnly ? 'Мои друзья' : 'Пользователи каталога' }}</h1>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('users.index') }}" class="btn {{ $friendsOnly ? 'btn-outline-primary' : 'btn-primary' }}">Все пользователи</a>
            <a href="{{ route('friends.index') }}" class="btn {{ $friendsOnly ? 'btn-primary' : 'btn-outline-primary' }}">Мои друзья</a>
        </div>
        <div class="card family-content">
            <div class="card-body p-4">
                <div class="list-group">
                    @forelse ($users as $user)
                        <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <a href="{{ route('users.turtles', $user) }}" class="text-decoration-none">
                                {{ $user->name }} @if ($user->is(auth()->user())) (вы) @endif
                                <span class="text-muted small d-block">{{ '@'.$user->username }}</span>
                            </a>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge text-bg-primary rounded-pill">Черепах: {{ $user->turtles_count }}</span>
                                @can('friends.manage', $user)
                                    @if (in_array($user->id, $friendIds))
                                        <form method="POST" action="{{ route('friends.destroy', $user) }}" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить из друзей</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('friends.store', $user) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Добавить в друзья</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ $friendsOnly ? 'У вас пока нет друзей.' : 'Пользователей пока нет.' }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
