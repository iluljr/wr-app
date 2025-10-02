<?php

namespace App\Http\Controllers;

use App\Models\RepetitiveActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepetitiveActivityController extends Controller
{
    public function index(Request $request): View
    {
        $activities = RepetitiveActivity::where('user_id', $request->user()->id)
            ->orderBy('start_time')
            ->orderBy('title')
            ->get();

        return view('repetitives.index', [
            'activities' => $activities,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'output' => 'nullable|string|max:2000',
        ]);

        RepetitiveActivity::create([
            'user_id' => $request->user()->id,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'title' => $data['title'],
            'output' => $data['output'] ?? null,
        ]);

        return redirect()->route('repetitives.index')->with('status', 'Aktivitas repetitif ditambahkan.');
    }

    public function update(Request $request, RepetitiveActivity $repetitive): RedirectResponse
    {
        if ($repetitive->user_id !== $request->user()->id) {
            abort(403, 'Tidak boleh mengubah aktivitas milik user lain.');
        }

        $data = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'output' => 'nullable|string|max:2000',
        ]);

        $repetitive->update([
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'title' => $data['title'],
            'output' => $data['output'] ?? null,
        ]);

        return redirect()->route('repetitives.index')->with('status', 'Aktivitas repetitif diperbarui.');
    }

    public function destroy(Request $request, RepetitiveActivity $repetitive): RedirectResponse
    {
        if ($repetitive->user_id !== $request->user()->id) {
            abort(403, 'Tidak boleh menghapus aktivitas milik user lain.');
        }

        $repetitive->delete();

        return redirect()->route('repetitives.index')->with('status', 'Aktivitas repetitif dihapus.');
    }
}
