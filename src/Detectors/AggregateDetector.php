<?php

namespace DetectAI\Detectors;

use DetectAI\AbstractDetector;
use DetectAI\DataTransferObjects\DetectionResultDTO;
use DetectAI\DetectorFactory;
use DetectAI\Enums\DetectionType;

class AggregateDetector extends AbstractDetector {
    private float $score;
    private array $detectionTypes;
    private array $reasons;
    private array $comparisonImages; // Array of UploadedImage or paths
    private array $metadata;
 
    public function detect(): DetectionResultDTO
    {
        $factory = new DetectorFactory($this->getFile(), $this->getOriginalFile());

        foreach(DetectionType::cases() as $case) {
            if ($case === DetectionType::AGGREGATE) {
                continue;
            }

            $dto = $factory->createDetector($case)->detect();

            $this->compileResults($dto);
        }

        $finalScore = $this->score / ( count(DetectionType::cases()) - 1 ); // -1 to Not Consider Aggregate detector case itself
        $finalScore = round($finalScore / 0.1) * 0.1; // TODO: Change step over codebase to be dynamic via config

        return (new DetectionResultDTO(
            $finalScore,
            DetectionType::AGGREGATE,
            $finalScore > 0.5 ? 'Highly likely that File was tempered with' : 'Unlikely that File wat tempered with',
        ));
    }

    private function compileResults(DetectionResultDTO $dto): void
    {
        $this->score += $dto->getScore();
        $this->detectionTypes[] = $dto->getDetectionType();
        $this->reasons[] = $dto->getReason();
        $this->comparisonImages = array_merge($this->comparisonImages, $dto->getComparisonImages());
        $this->metadata = array_merge($this->metadata, $dto->getMetadata());
    }
}