<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trash Kegiatan</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 900px; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: .5rem .75rem; text-align: left; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Trash Kegiatan</h1>

    @include('activities._flash')

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Dihapus pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td>{{ $activity->code }}</td>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->category->name }}</td>
                    <td>{{ $activity->status }}</td>
                    <td>{{ $activity->deleted_at->format('d M Y H:i') }}</td>
                    <td>
                        <form action="{{ route('activities.restore', $activity) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit">Restore</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Trash kosong.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $activities->links() }}

    <p><a href="{{ route('activities.index') }}">&larr; Kembali ke daftar kegiatan</a></p>
</body>
</html>
