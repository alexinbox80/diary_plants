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
     * @param string $directory
     * @return array
     */
    public function getFilesInDirectory(string $directory): array
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException("Directory does not exist: {$directory}");
        }

        $files = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            $path = $directory . '/' . $item;

            // Exclude '.' и '..'
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (is_file($path)) {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $directory
     * @param bool $moveFlag
     * @return File
     */
    public function storeUploadedFile(UploadedFile $uploadedFile, string $directory, bool $moveFlag = true): File
    {
        $directory = $this->uploadDirectory . '/' . $directory;

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf(
                    'Не удалось создать директорию: %s. Проверьте права доступа и существование родительской директории.',
                    $directory
                ));
            }
        }

        $fileName = sprintf('%s.%s', uniqid('image', true), $uploadedFile->getClientOriginalExtension());

        if ($moveFlag) {
            return $uploadedFile->move($directory, $fileName);
        } else {
            copy($uploadedFile->getRealPath(), $directory . '/' . $fileName);

            return new File($directory . '/' . $fileName);
        }

    }

    public function removeUploadedFile(string $file): bool
    {
        $file = $this->uploadDirectory . '/' . $file;

        if (file_exists($file)) {
            if (unlink($file)) {
                return true;
            } else {
                throw new \RuntimeException(sprintf(
                    'Не удалось удалить файл: %s. Проверьте права доступа и существование файла.',
                    $file
                ));
            }
        } else {
            throw new \RuntimeException(sprintf(
                'Не удалось удалить файл: %s.',
                $file
            ));
        }

        return false;
    }
}
