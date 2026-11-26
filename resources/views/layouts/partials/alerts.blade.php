@if (session('success'))
    <div class="alert alert-success mt-3" role="status">
        {{ session('success') }}
    </div>
@endif

@error('restore')
    <div class="alert alert-danger mt-3" role="alert">{{ $message }}</div>
@enderror
