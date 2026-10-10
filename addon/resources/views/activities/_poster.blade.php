{{-- Tampilkan poster bila ada. Butuh: php artisan storage:link --}}
@if ($activity->poster_path)
    <img src="{{ Storage::disk('public')->url($activity->poster_path) }}"
         alt="Poster {{ $activity->title }}"
         style="max-width:240px;height:auto;border:1px solid #ccc">
@endif
