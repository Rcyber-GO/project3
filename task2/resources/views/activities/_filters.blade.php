{{-- Form filter: method GET supaya parameter muncul di URL (bukti AC-06 s.d. AC-09) --}}
<form method="GET" action="{{ route('activities.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau kode">

    <select name="category_id">
        <option value="">Semua kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="status">
        <option value="">Semua status</option>
        @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>

    <select name="sort">
        <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option>
        <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
    </select>

    <button type="submit">Terapkan</button>
    <a href="{{ route('activities.index') }}">Reset</a>
</form>
