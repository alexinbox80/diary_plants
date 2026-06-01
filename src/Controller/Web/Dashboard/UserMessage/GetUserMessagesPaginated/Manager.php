<?php

namespace App\Controller\Web\Dashboard\UserMessage\GetUserMessagesPaginated;

use InvalidArgumentException;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\Service\UserMessageService;
use App\Application\Security\AccessContext;
use App\Domain\Model\UserMessage\UserMessageModel;

final class Manager
{
    public function __construct(
        private readonly UserMessageService $userMessageService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getUserMessagesPaginated(int $page, int $perPage): array
    {
        $userMessagesModel = $this->userMessageService->getUserMessagesPaginated($page, $perPage);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = UserMessageModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (UserMessageModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $userMessagesModel['userMessagesModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $userMessagesModel['pagination'],
        ];
    }
}
