<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Category;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __construct(private ActivityService $activities)
    {
    }

    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->with('category')   // eager loading: view index menampilkan nama kategori (hindari N+1)
            ->search($request->query('search'))
            ->inCategory($request->query('category_id'))
            ->withStatus($request->query('status'))
            ->sortByStart($request->query('sort'))
            ->paginate(10)
            ->withQueryString();

        return view('activities.index', [
            'activities' => $activities,
            'categories' => Category::ordered()->get(),
            'statuses' => Activity::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('activities.create', [
            'categories' => Category::ordered()->get(),
        ]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = $this->activities->createDraft($request->validated());

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil dibuat sebagai draft.');
    }

    public function show(Activity $activity): View
    {
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity): View
    {
        return view('activities.edit', [
            'activity' => $activity,
            'categories' => Category::ordered()->get(),
        ]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $this->activities->update($activity, $request->validated());

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Dengan trait SoftDeletes, delete() hanya mengisi deleted_at (bukan hapus permanen).
     */
    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan dipindahkan ke Trash dan dapat dipulihkan.');
    }

    public function trash(): View
    {
        $activities = Activity::onlyTrashed()
            ->with('category')
            ->latest('deleted_at')
            ->paginate(10);

        return view('activities.trash', compact('activities'));
    }

    /**
     * Route restore memakai ->withTrashed() supaya route model binding dapat menemukan record terhapus.
     */
    public function restore(Activity $activity): RedirectResponse
    {
        $activity->restore();

        return redirect()->route('activities.trash')
            ->with('success', "Kegiatan \"{$activity->title}\" berhasil dipulihkan.");
    }

    public function publish(Activity $activity): RedirectResponse
    {
        $this->activities->publish($activity);

        return back()->with('success', 'Kegiatan berhasil dipublikasikan.');
    }

    public function complete(Activity $activity): RedirectResponse
    {
        $this->activities->complete($activity);

        return back()->with('success', 'Kegiatan ditandai selesai.');
    }
}
