<?php

namespace DetectAI\Facades;

use DetectAI\Helpers\FileHelper;
use Illuminate\Support\Facades\Facade;

class File extends Facade {
    public static function getFacadeAccessor()
    {
        return FileHelper::class;
    }
}