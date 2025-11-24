<?php

namespace DetectAI\Interfaces;

use DetectAI\DataTransferObjects\DetectionResultDTO;
use Illuminate\Http\File;

interface DetectorInterface {
    public function getFile(): File;
    public function getOriginalFile(): ?File;
    public function detect(): DetectionResultDTO;
}