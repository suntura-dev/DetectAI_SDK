<?php

namespace DetectAI\Detectors;

use DetectAI\AbstractDetector;
use DetectAI\DataTransferObjects\DetectionResultDTO;
use DetectAI\Enums\DetectionType;
use Exception;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class HashDetector extends AbstractDetector
{
    public function detect(): DetectionResultDTO
    {
        $originalFileHash = hash_file('sha256', $this->getFile()->getRealPath());
        $fileHash = hash_file('sha256', $this->getOriginalFile()->getRealPath());

        if (!$originalFileHash || empty($originalFileHash)) {
            throw new Exception('Unable to generate hash for Original File');
        }

        if (!$fileHash || empty($fileHash)) {
            throw new Exception('Unable to generate hash for Analyzed File');
        }

        if ($originalFileHash !== $fileHash) {
            return $this->calculatePerceptualHash();
        }

        return new DetectionResultDTO(
            0.0,
            DetectionType::HASHING,
            'Images are identical',
            [ $this->getOriginalFile() ]
        );
    }

    private function calculatePerceptualHash(): DetectionResultDTO
    {
        $hasher = new ImageHash(new DifferenceHash());

        $hash1 = $hasher->hash($this->getFile()->getRealPath());
        $hash2 = $hasher->hash($this->getOriginalFile()->getRealPath());

        $distance = $hash1->distance($hash2);

        $score = $distance >= 10 ? 1.0 : $distance / 10;
        
        return new DetectionResultDTO(
            $score,
            DetectionType::HASHING,
            $score <= 0.5 ? 'Images are similar' : 'Image might have been tempered with' ,
            [ $this->getOriginalFile() ],
        );
    }
}