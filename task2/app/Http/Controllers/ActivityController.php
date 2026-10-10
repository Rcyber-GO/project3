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

/**
 * Controller sebagai orchestrator: menerima request, memanggil service/model, mengembalikan response.
 * Business rule ada di ActivityService, query list ada di query scope pada model Activity.
 */
class ActivityController extends Controller
{
    public function __construct(private ActivityService $activities)
    {
    }

    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->search($request->query('search'))
            ->inCategory($request->query('category_id'))
            ->withStatus($request->query('status'))
            ->sortByStart($request->query('sort'))
            ->paginate(10)
            ->withQueryString();   // parameter search/filter/sort tidak hilang saat pindah halaman

        return view('activities.index', [
            'activities' => $activities,
            'categories' => Category::ordered()->get(),
            'statuses' => ['draft', 'published', 'completed'],
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

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function publish(Activity $activity): RedirectResponse
    {
        $this->activities->publish($activity);   // ValidationException -> otomatis kembali dengan pesan error

        return back()->with('success', 'Kegiatan berhasil dipublikasikan.');
    }

    public function complete(Activity $activity): RedirectResponse
    {
        $this->activities->complete($activity);

        return back()->with('success', 'Kegiatan ditandai selesai.');
    }
}
