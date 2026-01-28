<?php

namespace App\Controller\Web\Dashboard\Image\GetImagesPaginated;

use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Service\AttachmentService;

class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAttachmentsPaginated(int $page, int $perPage): array
    {
        $attachmentsModel = $this->attachmentService->getAttachmentsPaginated($page, $perPage);
        $tableHeader = AttachmentModel::getTableHeaderRu();
        $tableBody = array_map(
            static fn (AttachmentModel $model): array => $model->toArray(),
            $attachmentsModel['attachmentsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $attachmentsModel['pagination'],
        ];
    }
}
