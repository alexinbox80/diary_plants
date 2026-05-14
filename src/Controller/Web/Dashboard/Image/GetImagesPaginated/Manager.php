<?php

namespace App\Controller\Web\Dashboard\Image\GetImagesPaginated;

use InvalidArgumentException;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\Service\AttachmentService;
use App\Application\Security\AccessContext;
use App\Domain\Model\Attachment\AttachmentModel;

final class Manager
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getAttachmentsPaginated(int $page, int $perPage): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $attachmentsModel = $this->attachmentService->getAttachmentsPaginatedByGroupId($page, $perPage, $groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = AttachmentModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (AttachmentModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $attachmentsModel['attachmentsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $attachmentsModel['pagination'],
        ];
    }
}
