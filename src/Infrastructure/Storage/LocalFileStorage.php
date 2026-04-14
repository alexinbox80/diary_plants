<?php

namespace App\Infrastructure\Storage;

use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalFileStorage
{
    private string $uploadDirectory;

    public function __construct(
        string $uploadDirectory,
        private readonly BuilderInterface $customQrCodeBuilder
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
                    'Failed to create directory: %s. Check permissions and the existence of the parent directory.',
                    $directory
                ));
            }
        }

        $fileName = sprintf('%s.%s', uniqid('image', true), $uploadedFile->getClientOriginalExtension());

        if ($moveFlag) {
            return $uploadedFile->move($directory, $fileName);
        } else {
            //copy($uploadedFile->getRealPath(), $directory . '/' . $fileName);
            copy($uploadedFile->getPathname(), $directory . '/' . $fileName);

            return new File($directory . '/' . $fileName);
        }

    }

    /**
     * @param string $path
     * @return bool
     */
    public function removeUploadedFile(string $path): bool
    {
        $file = $this->uploadDirectory . '/' . $path;

        if (file_exists($file)) {
            if (unlink($file)) {
                return true;
            } else {
                throw new \RuntimeException(sprintf(
                    'Failed to delete file: %s. Check permissions and file existence.',
                    $file
                ));
            }
        } else {
            throw new \RuntimeException(sprintf(
                'Failed to delete file: %s.',
                $file
            ));
        }
    }

    /**
     * @param string $path
     * @param string $uuid
     * @param string $url
     * @return string
     */
    public function createQrCodeFile(string $path, string $uuid, string $url): string
    {
        $directory = $this->uploadDirectory . '/' . $path;

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf(
                    'Failed to create directory: %s. Check permissions and the existence of the parent directory.',
                    $directory
                ));
            }
        }

        $fileName = $uuid . '.png';
        $fullPath = $directory . $fileName;

        // Используем билдер пакета
        $result = $this->customQrCodeBuilder->build(
            writer: new PngWriter(),
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
        );

        // Сохраняем файл
        $result->saveToFile($fullPath);

        return $path . $fileName;
    }
}
