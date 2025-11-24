<?php

namespace App\Facades;

use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Facade;

class File extends Facade {
    public static function getFacadeAccessor()
    {
        return FileHelper::class;
    }
}