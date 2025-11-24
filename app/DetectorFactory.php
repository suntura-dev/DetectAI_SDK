<?php

namespace App;

use App\Detectors\ExifDetector;
use App\Detectors\HashDetector;
use App\Detectors\OpenAIDetector;
use App\Enums\DetectionType;
use App\Facades\File as FileFacade;
use App\Interfaces\DetectorInterface;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

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