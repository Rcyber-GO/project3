<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kategori Kegiatan</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 720px; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: .5rem .75rem; text-align: left; }
        th { background: #f4f4f4; }
        .alert { padding: .75rem 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .alert-success { background: #e6f4ea; color: #1e6b34; }
        .alert-error { background: #fdeaea; color: #a12626; }
        button { cursor: pointer; }
    </style>
</head>
<body>
    <h1>Kategori Kegiatan</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Slug</th>
                <th>Jumlah Kegiatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->activities_count }}</td>
                    <td>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada kategori.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p><a href="{{ route('activities.index') }}">&larr; Kembali ke daftar kegiatan</a></p>
</body>
</html>
