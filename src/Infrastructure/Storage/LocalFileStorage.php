<?php

namespace App\Infrastructure\Storage;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalFileStorage
{
    private string $uploadDirectory;

    public function __construct(
        string $uploadDirectory
    ) {
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $directory
     * @return File
     */
    public function storeUploadedFile(UploadedFile $uploadedFile, string $directory): File
    {
        $directory = $this->uploadDirectory . '/attachments/' . $directory;

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf(
                    'Не удалось создать директорию: %s. Проверьте права доступа и существование родительской директории.',
                    $directory
                ));
            }
        }

        $fileName = sprintf('%s.%s', uniqid('image', true), $uploadedFile->getClientOriginalExtension());

        return $uploadedFile->move($directory, $fileName);
    }
}
