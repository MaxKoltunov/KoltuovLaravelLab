<section id="comments" class="family-info mt-4 mb-4" aria-labelledby="comments-title">
    <h2 id="comments-title" class="family-title">Комментарии ({{ $turtle->comments->count() }})</h2>

    <div class="card family-content">
        <div class="card-body p-4">
            @forelse ($turtle->comments as $comment)
                <article class="border-bottom pb-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div>
                            @auth
                                <a href="{{ route('users.turtles', $comment->user) }}"
                                   class="fw-semibold text-decoration-none">{{ $comment->user->name }}</a>
                            @else
                                <span class="fw-semibold">{{ $comment->user->name }}</span>
                            @endauth
                            <span class="small text-muted">{{ '@'.$comment->user->username }}</span>
                        </div>
                        <time datetime="{{ $comment->created_at->toIso8601String() }}" class="small text-muted">
                            {{ $comment->created_at->format('d.m.Y H:i') }}
                        </time>
                    </div>
                    <p class="mb-0 text-break" style="white-space: pre-wrap;">{{ $comment->text }}</p>
                </article>
            @empty
                <p class="text-muted">Комментариев пока нет.</p>
            @endforelse

            @can('comments.create', $turtle)
                <form method="POST" action="{{ route('comments.store', $turtle) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="comment-text" class="form-label fw-semibold">Оставить комментарий</label>
                        <textarea id="comment-text" name="text" rows="3" maxlength="2000" required
                                  class="form-control @error('text') is-invalid @enderror"
                                  placeholder="Напишите комментарий...">{{ old('text') }}</textarea>
                        @error('text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Не более 2000 символов.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить комментарий</button>
                </form>
            @else
                @if ($turtle->trashed())
                    <p class="text-muted mb-0">К удалённой записи нельзя добавлять комментарии.</p>
                @else
                    <p class="mb-0">
                        <a href="{{ route('login') }}">Войдите</a>, чтобы оставить комментарий.
                    </p>
                @endif
            @endcan
        </div>
    </div>
</section>
