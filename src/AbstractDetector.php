<?php

namespace DetectAI;

use DetectAI\Facades\File as FileFacade;
use DetectAI\Interfaces\DetectorInterface;
use Illuminate\Http\File;

abstract class AbstractDetector implements DetectorInterface {
    private File $file;
    private ?File $originalFile;

    public function __construct(File|string $file, File|string|null $originalFile = null)
    {
        $this->file = FileFacade::create($file);
        $this->originalFile = FileFacade::create($originalFile);
    }


    public function getFile(): File
    {
        return $this->file;
    }

    public function getOriginalFile(): ?File
    {
        return $this->originalFile;
    }
}