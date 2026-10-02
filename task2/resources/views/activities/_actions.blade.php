{{-- Tombol aksi status. Dipakai di index/show: @include('activities._actions', ['activity' => $activity]) --}}
@if ($activity->status === 'draft')
    <form action="{{ route('activities.publish', $activity) }}" method="POST" style="display:inline">
        @csrf
        <button type="submit">Publish</button>
    </form>
@elseif ($activity->status === 'published')
    <form action="{{ route('activities.complete', $activity) }}" method="POST" style="display:inline">
        @csrf
        <button type="submit">Complete</button>
    </form>
@endif
