<?php

namespace App\Interfaces;

use App\DataTransferObjects\DetectionResultDTO;
use Illuminate\Http\File;

interface DetectorInterface {
    public function getFile(): File;
    public function getOriginalFile(): ?File;
    public function detect(): DetectionResultDTO;
}