<?php

namespace DetectAI\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FileHelper {
    public function create(File|string $file): File
    {
        if ($file instanceof File) return $file;

        if (Str::isUrl($file)) {
            $contents = file_get_contents($file);
            $uniqueName = hash_file('sha256', $contents);

            Storage::disk('temp')->put($uniqueName, $contents);
            
            return new File(Storage::disk('temp')->path($uniqueName));
        }

        return new File($file);
    }
}