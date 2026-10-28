{{-- Общая форма для create и edit: при редактировании передаём существующий $turtle. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Название черепахи *</label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $turtle?->name ?? '') }}"
               maxlength="255" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="scientific_name" class="form-label">Латинское название *</label>
        <input type="text" name="scientific_name" id="scientific_name"
               class="form-control @error('scientific_name') is-invalid @enderror"
               value="{{ old('scientific_name', $turtle?->scientific_name ?? '') }}"
               maxlength="255" required
               placeholder="Testudo graeca">
        @error('scientific_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="category" class="form-label">Категория *</label>
        <select name="category" id="category"
                class="form-select @error('category') is-invalid @enderror" required>
            <option value="">Выберите категорию</option>
            @foreach (['Морская', 'Пресноводная', 'Сухопутная'] as $category)
                <option value="{{ $category }}"
                    @selected(old('category', $turtle?->category ?? '') === $category)>
                    {{ $category }}
                </option>
            @endforeach
        </select>
        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="catalogued_at" class="form-label">Дата добавления в каталог</label>
        <input type="date" name="catalogued_at" id="catalogued_at"
               class="form-control @error('catalogued_at') is-invalid @enderror"
               value="{{ old('catalogued_at', $turtle?->catalogued_at?->format('Y-m-d') ?? today()->toDateString()) }}">
        @error('catalogued_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="short_description" class="form-label">Краткое описание *</label>
        <textarea name="short_description" id="short_description"
                  class="form-control @error('short_description') is-invalid @enderror"
                  rows="3" maxlength="2000" required>{{ old('short_description', $turtle?->short_description ?? '') }}</textarea>
        @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="intro_description" class="form-label">Вводное описание *</label>
        <textarea name="intro_description" id="intro_description"
                  class="form-control @error('intro_description') is-invalid @enderror"
                  rows="3" maxlength="5000" required>{{ old('intro_description', $turtle?->intro_description ?? '') }}</textarea>
        @error('intro_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="full_description" class="form-label">Подробное описание *</label>
        <textarea name="full_description" id="full_description"
                  class="form-control @error('full_description') is-invalid @enderror"
                  rows="6" maxlength="10000" required>{{ old('full_description', $turtle?->full_description ?? '') }}</textarea>
        @error('full_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="image" class="form-label">Изображение черепахи</label>
        <input type="file" name="image" id="image"
               class="form-control @error('image') is-invalid @enderror"
               accept="image/jpeg,image/png,image/webp">
        <div class="form-text">JPG, PNG или WebP, не более 5 МБ. После загрузки изображение уменьшается до 1200×900 пикселей с сохранением пропорций. Поле необязательное.</div>
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

        @if ($turtle?->image_path)
            <img src="{{ asset('storage/' . $turtle->image_path) }}"
                 alt="Текущее изображение"
                 class="img-thumbnail mt-2" style="max-width: 240px;">
        @endif
    </div>
</div>
