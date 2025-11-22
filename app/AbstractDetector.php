<?php

namespace App;

use App\Interfaces\DetectorInterface;
use Illuminate\Http\UploadedFile;

abstract class AbstractDetector implements DetectorInterface {
    private UploadedFile $file;

    public function __construct(UploadedFile|string $file)
    {
        $this->file = $file instanceof UploadedFile ? $file : new UploadedFile($file, now());
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}