<?php

namespace App\Interfaces;

use App\DataTransferObjects\DetectionResultDTO;
use Illuminate\Http\UploadedFile;


interface DetectorInterface {
    public function getFile(): UploadedFile;
    public function detect(): DetectionResultDTO;
}