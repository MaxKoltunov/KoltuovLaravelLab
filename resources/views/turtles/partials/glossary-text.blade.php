@php
    // Разбиваем текст на обычные фрагменты и слово с подсказкой.
    // Все фрагменты выводятся через {{ }}, чтобы сохранить HTML-экранирование.
    $term = ($glossary['field'] ?? null) === $field ? $glossary['term'] : null;
    $segments = $term
        ? preg_split('/(' . preg_quote($term, '/') . ')/iu', $content, -1, PREG_SPLIT_DELIM_CAPTURE)
        : [$content];
@endphp
@foreach ($segments as $segment)
    @if ($term !== null && mb_strtolower($segment) === mb_strtolower($term))
        <button type="button"
                class="btn btn-link p-0 align-baseline"
                data-bs-toggle="popover"
                data-bs-title="{{ $glossary['title'] }}"
                data-bs-content="{{ $glossary['definition'] }}">{{ $segment }}</button>
    @else
        {{ $segment }}
    @endif
@endforeach
