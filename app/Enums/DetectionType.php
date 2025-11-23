<?php

namespace App\Enums;

enum DetectionType: string {
    case HASHING = 'Hashing';
    case EXIF_ANALYSIS = 'Exif';
}