<?php

namespace App\Controller\Web\Dashboard\Image\GetImages;

use InvalidArgumentException;
use App\Domain\Service\AttachmentService;
use App\Domain\ValueObject\Enum\Timezone;
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
     * @return array
     * @throws InvalidArgumentException
     */
    public function getAttachments(): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $attachmentsModel = $this->attachmentService->findAllByGroupId($groupId);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = AttachmentModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (AttachmentModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $attachmentsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}
