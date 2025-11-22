<?php

namespace App\DataTransferObjects;

use App\Enums\DetectionType;

class DetectionResultDTO {
    private float $score;
    private DetectionType $detectionType;
    private string $reason;
    private array $comparisonImages; // Array of UploadedImage or paths
    private array $metadata;

    public function __construct(
        float $score,
        DetectionType $detectionType,
        string $reason,
        array $comparisonImages = [],
        array $metadata = [],
    )
    {
        $this->score = $score;
        $this->detectionType = $detectionType;
        $this->reason = $reason;
        $this->comparisonImages = $comparisonImages;
        $this->metadata = $metadata;
    }

    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'detection_type' => $this->detectionType,
            'reason' => $this->reason,
            'comparison_images' => $this->comparisonImages,
            'metadata' => $this->metadata,
        ];
    }
}