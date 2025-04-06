<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $path = $file->path;
        if (!Storage::exists($path)) {
            return response()->json(['message' => 'File not found on server'], 404);
        }

        // Получаем содержимое файла для скачивания
        return response()->download(storage_path('app/public/' . $file->path), $file->name);
    }
}
