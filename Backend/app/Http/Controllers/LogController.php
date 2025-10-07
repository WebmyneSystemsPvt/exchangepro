<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    public function showLogs()
    {
        $logFilePath = storage_path('logs/laravel.log');

        if (!File::exists($logFilePath)) {
            return response()->view('logs.show', ['content' => 'Log file does not exist.']);
        }

        $content = File::get($logFilePath);
        return response()->view('logs.show', ['content' => nl2br(e($content))]);
    }

    public function listJsonFiles()
    {
        $directory = storage_path('logs/newFileStorage');
        if (!File::isDirectory($directory)) {
            return response()->json(['error' => 'Directory not found.'], 404);
        }

        $files = File::files($directory);
        $jsonFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'json';
        });

        $fileNames = array_map(function($file) {
            return pathinfo($file, PATHINFO_BASENAME);
        }, $jsonFiles);

        return response()->json($fileNames);
    }

    public function viewJsonFile($filename)
    {
        $filePath = storage_path('logs/newFileStorage/' . $filename);

        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        $content = File::get($filePath);
        return response()->json(json_decode($content, true));
    }

    public function clearLogs()
    {
        $logFilePath = storage_path('logs/laravel.log');

        if (File::exists($logFilePath)) {
            File::put($logFilePath, ''); // Clear the log file
            Log::info('Log file cleared by user.');
        }

        return redirect()->route('show.logs')->with('status', 'Log file cleared successfully.');
    }
}
