<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Activity;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(private RegistrationService $registrations)
    {
    }

    public function create(Activity $activity): View
    {
        return view('registrations.create', compact('activity'));
    }

    public function store(StoreRegistrationRequest $request, Activity $activity): RedirectResponse
    {
        $this->registrations->register($activity, $request->validated());

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Pendaftaran berhasil.');
    }
}
