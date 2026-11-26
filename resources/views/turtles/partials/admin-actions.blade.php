@can('turtles.restore', $turtle)
    <form method="POST" action="{{ route('turtles.restore', $turtle) }}">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-outline-success">Восстановить</button>
    </form>
@endcan

@can('turtles.force-delete', $turtle)
    <form method="POST" action="{{ route('turtles.force-delete', $turtle) }}"
          onsubmit="return confirm('Удалить запись навсегда? Восстановить её будет невозможно.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Удалить навсегда</button>
    </form>
@endcan
