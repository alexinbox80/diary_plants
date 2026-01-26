<?php

namespace App\Controller\Web\Admin\Image\DeleteImage;

use App\Domain\Service\AttachmentService;
use App\Domain\Service\FileService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly FileService $fileService
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $attachment = $this->attachmentService->find($id);

        if (!is_null($attachment->getPath()) || !is_null($attachment->getFilename())) {
            $this->fileService->removeUploadedFile($attachment->getPath(). $attachment->getFilename());
            $this->attachmentService->removeAttachment($attachment);
        } else {
            $this->attachmentService->removeById($id);
        }

        $request->getSession()->getFlashBag()->add('success', 'Изображение успешно удалено.');
        return ['success' => true];
    }
}
