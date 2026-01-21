<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttachmentController extends Controller
{
    public function download($filename)
    {
        Log::info("Download request received", ['filename' => $filename, 'user_id' => auth()->id()]);
    
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }
    
        $path = public_path('attachments/' . $filename);
    
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }
    
        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $filename);
    }    
    
}
