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
     * @param string $directory
     * @return array
     */
    public function getFilesInDirectory(string $directory): array
    {
        return $this->localFileStorage->getFilesInDirectory($directory);
    }

    /**
     * @param string $attachableType
     * @param int $attachableId
     * @return string
     */
    public function getAttachmentsPath(string $attachableType, int $attachableId): string
    {
        $entity = explode('::', $attachableType);
        $entity = array_shift($entity);

        return ('attachments/' . $entity . '/' . $attachableId . '/');
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $directory
     * @param bool $moveFlag
     * @return File
     */
    public function storeUploadedFile(UploadedFile $uploadedFile, string $directory, bool $moveFlag = true): File
    {
        return $this->localFileStorage->storeUploadedFile($uploadedFile, $directory, $moveFlag);
    }

    /**
     * @param string $file
     * @return bool
     */
    public function removeUploadedFile(string $file): bool
    {
        return $this->localFileStorage->removeUploadedFile($file);
    }
}
