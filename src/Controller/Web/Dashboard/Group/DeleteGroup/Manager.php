<?php

namespace App\Controller\Web\Dashboard\Group\DeleteGroup;

use App\Domain\Service\GroupService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Manager
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function deleteData(int $id, Request $request): array
    {
        $this->groupService->removeById($id);

        $message = $this->translator->trans('plant.flash.deleted', [], 'messages');
        $request->getSession()->getFlashBag()->add('success', $message);

        return ['success' => true];
    }
}
