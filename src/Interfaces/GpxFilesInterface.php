<?php

namespace App\Interfaces;

interface GpxFilesInterface
{
    public static function getRelativeBasePathToGpxFiles(): string;

    public function getRelativePathToGpxFiles(): string;
}