<?php

namespace App\DataTransferObjects;

use App\Enums\DetectionType;

class DetectionResultDTO {
    private float $score;
    private DetectionType $detectionType;
    private string $reason;
    private array $metadata;
    private array $comparisonImages; // Array of UploadedImage or paths

    public function __construct(
        float $score,
        DetectionType $detectionType,
        string $reason,
        array $metadata = [],
        array $comparisonImages = [],
    )
    {
        $this->score = $score;
        $this->detectionType = $detectionType;
        $this->reason = $reason;
        $this->metadata = $metadata;
        $this->comparisonImages = $comparisonImages;
    }

    public function toArray(): array
    {
        return [];
    }
}