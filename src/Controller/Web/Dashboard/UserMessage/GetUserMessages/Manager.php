<?php

namespace App\Controller\Web\Dashboard\UserMessage\GetUserMessages;

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
     * @return array
     * @throws InvalidArgumentException
     */
    public function getUserMessages(): array
    {
        $userMessagesModel = $this->userMessageService->findAll();

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = UserMessageModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (UserMessageModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $userMessagesModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}
