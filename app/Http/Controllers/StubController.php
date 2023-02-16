<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;


class StubController extends Controller
{
    public static $namespace, $fileSystem, $fileName, $name, $fullPath;

    public static function init($fileSystem, $namespace, $fileName, $name, $fullPath)
    {
        self::$namespace = $namespace;
        self::$fileName = $fileName;
        self::$name = $name;
        self::$fullPath = $fullPath;
        self::$fileSystem = $fileSystem;
    }

    public static function makeDir()
    {
        if (!self::$fileSystem->isDirectory(self::$fullPath)) self::$fileSystem->makeDirectory(self::$fullPath);
    }

    public function getSourceFile()
    {
        // $file = file
    }
}
