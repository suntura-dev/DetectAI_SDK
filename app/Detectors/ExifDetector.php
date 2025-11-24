<?php

namespace App\Detectors;

use App\AbstractDetector;
use App\Enums\DetectionType;
use Illuminate\Http\UploadedFile;
use App\DataTransferObjects\DetectionResultDTO;

class ExifDetector extends AbstractDetector {
    private array $requiredKeys = [
        'ImageDescription',
        'Artist',
        'By-line',
        'Copyright',
        'Credit',
        'XResolution',
        'YResolution',
        'ResolutionUnit',
        'DateTimeOriginal',
        'Make',
        'Model'
    ];
    private array $exifKeys = [];

    public function __construct(
        UploadedFile|string $file,
        UploadedFile|string|null $originalFile = null,
        ?array $additionalKeysToCheck = null)
    {
        parent::__construct($file, $originalFile);
        
        if (!empty($additionalKeysToCheck)) {
            $this->requiredKeys = array_merge($this->requiredKeys, $additionalKeysToCheck);
        }
    }

    public function detect(): DetectionResultDTO
    {
        $exif = @exif_read_data($this->getFile()->getRealPath(), 0, true);

        if (empty($exif)) {
            return (new DetectionResultDTO(
                0.6,
                DetectionType::EXIF_ANALYSIS,
                'File may have been tempered with due to lack of EXIF metadata',
            ));
        }

        array_walk_recursive($exif, function($value, $key) {
            $this->exifKeys[$key] = $value;
        });

        $finalScore = $this->getMissingKeysCount() + $this->getAnomaliesCount();
        return (new DetectionResultDTO(
            $finalScore,
            DetectionType::EXIF_ANALYSIS,
            $finalScore === 0.0
                ? 'File has not been tempered with'
                : 'File may gave been tempered with due to EXIF metadata suspicions'
        ));
    }

    private function getMissingKeysCount(): float
    {
        $missingCount = 0;

        foreach ($this->requiredKeys as $key) {
            if (empty($this->exifKeys[$key])) {
                $missingCount++;
            }
        }

        return $missingCount >= 5 ? 0.5 : 0.0;
    }

    private function getAnomaliesCount(): int
    {
        $anomalies = 0;

        if (isset($this->exifKeys['XResolution']) && (int) $this->exifKeys['XResolution'] === 1) {
            $anomalies++;
        }

        if (isset($this->exifKeys['YResolution']) && (int) $this->exifKeys['YResolution'] === 1) {
            $anomalies++;
        }

        if (empty($this->exifKeys['ResolutionUnit'])) {
            $anomalies++;
        }

        if (!empty($this->exifKeys['EncodingProcess']) && stripos($this->exifKeys['EncodingProcess'], 'progressive') !== false) {
            $anomalies++;
        }

        // Many editing apps add 'Software' tag
        if (!empty($this->exifKeys['Software'])) {
            $anomalies++;
        }

        return $anomalies / 10;
    }
}