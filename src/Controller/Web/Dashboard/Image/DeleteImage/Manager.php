<?php

namespace App\Controller\Web\Dashboard\Image\DeleteImage;

use App\Domain\Service\AttachmentService;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService
    ) {
    }

    public function deleteData(int $id, Request $request): array
    {
        $this->attachmentService->deleteWithFile($id);

        $request->getSession()->getFlashBag()->add('success', 'Изображение успешно удалено.');
        return ['success' => true];
    }
}
