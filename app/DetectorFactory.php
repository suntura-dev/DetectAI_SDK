<?php

namespace App;

use App\Detectors\ExifDetector;
use App\Detectors\HashDetector;
use App\Detectors\OpenAIDetector;
use App\Enums\DetectionType;
use App\Interfaces\DetectorInterface;
use Illuminate\Http\UploadedFile;

class DetectorFactory {
    private UploadedFile $file;
    private ?UploadedFile $originalFile;

    public function __construct(UploadedFile|string $file, UploadedFile|string|null $originalFile = null)
    {
        $this->file = $file instanceof UploadedFile ? $file : new UploadedFile($file, now());

        if ($originalFile) {
            $this->originalFile = $originalFile instanceof UploadedFile
                ? $originalFile
                : new UploadedFile($originalFile, now());
        }
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