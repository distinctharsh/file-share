<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $files = array_reverse(Storage::disk('public')->files('uploads'));
        return view('welcome', compact('files'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // Max 100MB (Aap apne hisab se badha sakte hain)
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads', $filename, 'public');
        }

        return back()->with('success', 'Upload successfully');
    }

    public function download($filename)
    {
        $filePath = 'uploads/' . $filename;
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }

        return back()->with('error', 'File nahi mili!');
    }
}