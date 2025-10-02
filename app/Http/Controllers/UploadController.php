<?php

namespace App\Http\Controllers;

use App\Jobs\ParseUploadJob;
use App\Models\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $uploads = Upload::with('user')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('uploads.index', compact('uploads'));
    }

    public function create()
    {
        return view('uploads.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'kind' => 'required|in:resolved,actual_end',
            'for_month' => 'required|date',
        ]);

        $path = $request->file('file')->store('uploads');

        $upload = Upload::create([
            'user_id' => $request->user()->id,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'stored_path' => $path,
            'kind' => $data['kind'],
            'for_month' => Carbon::parse($data['for_month'])->startOfMonth(),
        ]);

        dispatch(new ParseUploadJob($upload->id));

        return redirect()->route('uploads.index')->with('status', 'File diunggah, diproses di background.');
    }

    public function destroy(Request $request, Upload $upload)
    {
        if ($upload->user_id !== $request->user()->id) {
            abort(403, 'Tidak boleh menghapus upload milik user lain.');
        }

        Storage::delete($upload->stored_path);

        $upload->delete();

        return redirect()->route('uploads.index')->with('status', 'Upload berhasil dihapus.');
    }
}
