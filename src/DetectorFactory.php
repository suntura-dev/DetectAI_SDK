<?php

namespace DetectAI;

use DetectAI\Detectors\ExifDetector;
use DetectAI\Detectors\HashDetector;
use DetectAI\Detectors\OpenAIDetector;
use DetectAI\Enums\DetectionType;
use DetectAI\Facades\File as FileFacade;
use DetectAI\Interfaces\DetectorInterface;
use Illuminate\Http\File;

class DetectorFactory {
    private File $file;
    private ?File $originalFile;

    public function __construct(File|string $file, File|string|null $originalFile = null)
    {
        $this->file = FileFacade::create($file);
        $this->originalFile = FileFacade::create($originalFile);
    }

    public function createDetector(DetectionType|string $type): ?DetectorInterface
    {
        if (!$type instanceof DetectionType) {
            $type = DetectionType::tryFrom($type);
        }

        return match ($type) {
            DetectionType::HASHING => new HashDetector($this->file, $this->originalFile),
            DetectionType::EXIF_ANALYSIS => new ExifDetector($this->file),
            DetectionType::OPENAI_API => new OpenAIDetector($this->file),
            'default' => null
        };
    }
}