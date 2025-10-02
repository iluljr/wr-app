<?php

namespace App\Http\Controllers;

use App\Models\ManualActivity;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManualActivityController extends Controller
{
    public function index(Request $request): View
    {
        $selectedDate = $request->input('for_date')
            ? Carbon::parse($request->input('for_date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $activities = ManualActivity::with('user')
            ->where('user_id', $request->user()->id)
            ->whereDate('activity_date', $selectedDate->toDateString())
            ->orderBy('start_time')
            ->get();

        return view('activities.index', [
            'activities' => $activities,
            'selectedDate' => $selectedDate,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'activity_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'output' => 'nullable|string|max:2000',
        ]);

        ManualActivity::create([
            'user_id' => $request->user()->id,
            'activity_date' => $data['activity_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'title' => $data['title'],
            'output' => $data['output'] ?? null,
        ]);

        return redirect()
            ->route('activities.index', ['for_date' => Carbon::parse($data['activity_date'])->toDateString()])
            ->with('status', 'Aktivitas berhasil disimpan.');
    }

    public function update(Request $request, ManualActivity $activity): RedirectResponse
    {
        if ($activity->user_id !== $request->user()->id) {
            abort(403, 'Tidak boleh mengubah aktivitas milik user lain.');
        }

        $data = $request->validate([
            'activity_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'output' => 'nullable|string|max:2000',
        ]);

        $activity->update([
            'activity_date' => $data['activity_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'title' => $data['title'],
            'output' => $data['output'] ?? null,
        ]);

        return redirect()
            ->route('activities.index', ['for_date' => Carbon::parse($data['activity_date'])->toDateString()])
            ->with('status', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy(Request $request, ManualActivity $activity): RedirectResponse
    {
        if ($activity->user_id !== $request->user()->id) {
            abort(403, 'Tidak boleh menghapus aktivitas milik user lain.');
        }

        $activityDate = Carbon::parse($activity->activity_date)->toDateString();
        $activity->delete();

        return redirect()
            ->route('activities.index', ['for_date' => $activityDate])
            ->with('status', 'Aktivitas berhasil dihapus.');
    }
}
