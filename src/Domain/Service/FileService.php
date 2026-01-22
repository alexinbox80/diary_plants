<?php

namespace App\Domain\Service;

use App\Infrastructure\Storage\LocalFileStorage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    public function __construct(
        private readonly LocalFileStorage $localFileStorage
    ) {
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $directory
     * @return File
     */
    public function storeUploadedFile(UploadedFile $uploadedFile, string $directory): File
    {
        return $this->localFileStorage->storeUploadedFile($uploadedFile, $directory);
    }
}
