<?php

namespace App\Detectors;

use App\AbstractDetector;
use App\DataTransferObjects\DetectionResultDTO;
use App\Enums\DetectionType;
use Exception;

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

        return (new DetectionResultDTO(
            $originalFileHash === $fileHash ? 0.0 : 0.5,
            DetectionType::HASHING,
            $originalFileHash === $fileHash
                ? 'File Hashes are identical'
                : 'File Hashes are not identical, file may or may not have been tempered with',
            [ $this->getOriginalFile() ]
        ));
    }
}