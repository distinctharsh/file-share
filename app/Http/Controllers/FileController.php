<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    private $chatFile = 'messages.json';

    public function index()
    {
        $files = Storage::disk('public')->files('uploads');
        
        $fileList = collect($files)->map(function ($file) {
            $filename = basename($file);
            $filePath = 'uploads/' . $filename;
            
            $time = Storage::disk('public')->exists($filePath) ? Storage::disk('public')->lastModified($filePath) : time();

            return [
                'name' => $filename,
                'size' => Storage::disk('public')->exists($filePath) ? Storage::disk('public')->size($filePath) : 0,
                'ext'  => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
                'date_formatted' => \Carbon\Carbon::createFromTimestamp($time)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A'),
            ];
        })->sortByDesc('date_formatted')->values()->all();

        // --- Storage Calculation ---
        $storageInfo = $this->getStorageDetails();

        return view('welcome', [
            'files'       => $fileList,
            'storageInfo' => $storageInfo
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $filename, 'public');

            $bytes = Storage::disk('public')->size($filePath);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $time = Storage::disk('public')->lastModified($filePath);

            if ($bytes >= 1048576) { $sizeStr = number_format($bytes / 1048576, 2) . ' MB'; }
            elseif ($bytes >= 1024) { $sizeStr = number_format($bytes / 1024, 2) . ' KB'; }
            else { $sizeStr = $bytes . ' bytes'; }

            $storageInfo = $this->getStorageDetails();

            $newFileData = [
                'name' => $filename,
                'ext' => $ext,
                'size' => $sizeStr,
                'date_formatted' => \Carbon\Carbon::createFromTimestamp($time)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A'),
                'download_url' => route('file.download', $filename),
                'delete_url' => route('file.delete', $filename),
            ];

            // Directly return JSON for AJAX
            return response()->json([
                'success' => true,
                'message' => 'File successfully upload ho gayi!',
                'file' => $newFileData,
                'storageInfo' => $storageInfo
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Upload fail ho gaya!'], 400);
    }

    public function download($filename)
    {
        $filePath = 'uploads/' . $filename;
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }

        return back()->with('error', 'File nahi mili!');
    }

    public function delete($filename)
    {
        $filePath = 'uploads/' . $filename;
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return back()->with('success', 'File delete ho gayi!');
        }

        return back()->with('error', 'File nahi mili ya delete nahi ho saki!');
    }

    // --- JSON Chat Methods ---

    public function getMessages()
    {
        if (!Storage::exists($this->chatFile)) {
            return response()->json([]);
        }

        $messages = json_decode(Storage::get($this->chatFile), true) ?? [];
        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $messages = [];
        if (Storage::exists($this->chatFile)) {
            $messages = json_decode(Storage::get($this->chatFile), true) ?? [];
        }

        $newMessage = [
            'id' => (string) (time() . rand(1000, 9999)),
            'message' => e($request->message),
            'ip' => $request->ip(),
            'time' => date('h:i A'),
        ];

        $messages[] = $newMessage;

        if (count($messages) > 100) {
            $messages = array_slice($messages, -100);
        }

        Storage::put($this->chatFile, json_encode($messages, JSON_PRETTY_PRINT));

        return response()->json(['success' => true, 'data' => $newMessage]);
    }

    public function deleteMessage(Request $request)
    {
        $id = $request->input('id');

        if (Storage::exists($this->chatFile)) {
            $messages = json_decode(Storage::get($this->chatFile), true) ?? [];
            
            $filteredMessages = array_values(array_filter($messages, function ($msg) use ($id) {
                return (string)($msg['id'] ?? '') !== (string)$id;
            }));

            Storage::put($this->chatFile, json_encode($filteredMessages, JSON_PRETTY_PRINT));
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    private function getStorageDetails()
    {
        $storagePath = storage_path('app/public');
        $freeSpace  = disk_free_space($storagePath);  
        $totalSpace = disk_total_space($storagePath); 
        $usedSpace  = $totalSpace - $freeSpace;      

        $usedPercentage = round(($usedSpace / $totalSpace) * 100, 1);

        return [
            'total'      => number_format($totalSpace / (1024 * 1024 * 1024), 2) . ' GB',
            'used'       => number_format($usedSpace / (1024 * 1024 * 1024), 2) . ' GB',
            'free'       => number_format($freeSpace / (1024 * 1024 * 1024), 2) . ' GB',
            'percentage' => $usedPercentage,
        ];
    }
}