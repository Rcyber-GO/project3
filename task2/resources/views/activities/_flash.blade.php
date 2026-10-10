{{-- Pesan sukses / error. Termasuk error dari ValidationException service (kunci "status"). --}}
@if (session('success'))
    <div style="padding:.75rem 1rem;margin-bottom:1rem;border-radius:4px;background:#e6f4ea;color:#1e6b34">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="padding:.75rem 1rem;margin-bottom:1rem;border-radius:4px;background:#fdeaea;color:#a12626">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div style="padding:.75rem 1rem;margin-bottom:1rem;border-radius:4px;background:#fdeaea;color:#a12626">
        <ul style="margin:0;padding-left:1.25rem">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
