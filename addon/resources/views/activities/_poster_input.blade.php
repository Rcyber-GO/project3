{{-- Input poster untuk form create/edit. Form WAJIB memakai enctype="multipart/form-data". --}}
<div>
    <label for="poster">Poster (opsional, JPG/PNG/WEBP, maks 2 MB)</label>
    <input type="file" name="poster" id="poster" accept="image/png,image/jpeg,image/webp">
    @error('poster')
        <small style="color:red">{{ $message }}</small>
    @enderror

    @isset($activity)
        @if ($activity->poster_path)
            <p>Poster saat ini:</p>
            @include('activities._poster', ['activity' => $activity])
            <small>Pilih file baru untuk mengganti; file lama akan dihapus setelah file baru tersimpan.</small>
        @endif
    @endisset
</div>
