<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Kegiatan</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 520px; padding: 0 1rem; }
        label { display: block; margin-top: 1rem; }
        input { width: 100%; padding: .5rem; box-sizing: border-box; }
        button { margin-top: 1.25rem; }
    </style>
</head>
<body>
    <h1>Daftar: {{ $activity->title }}</h1>
    <p>
        Kode {{ $activity->code }} &middot; status {{ $activity->status }}<br>
        Terisi {{ $activity->registered_count }} / {{ $activity->capacity }} peserta
    </p>

    @include('activities._flash')

    <form action="{{ route('registrations.store', $activity) }}" method="POST">
        @csrf

        <label for="participant_name">Nama</label>
        <input type="text" id="participant_name" name="participant_name" value="{{ old('participant_name') }}" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>

        <button type="submit">Daftar</button>
    </form>

    <p><a href="{{ route('activities.show', $activity) }}">&larr; Kembali ke detail kegiatan</a></p>
</body>
</html>
