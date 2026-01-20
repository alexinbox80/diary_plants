<?php

namespace App\Controller\Web\Admin\Image\GetImages;

use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Service\AttachmentService;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPlants(): array
    {
        $attachmentsModel = $this->attachmentService->findAll();
        $tableHeader = AttachmentModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (AttachmentModel $model): array => $model->toArray(),
            $attachmentsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}
