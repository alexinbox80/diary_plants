<?php

namespace App\Controller\Web\Dashboard\Group\CreateGroup;

use App\Controller\Form\GroupType;
use App\Domain\Service\GroupService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Group\CreateGroup\Input\CreateGroupDTO;

class Manager
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(GroupType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateGroupDTO $createGroupDTO */
            $createGroupDTO = $form->getData();

            $this->groupService->createFromCreateGroupDTO($createGroupDTO);

            $request->getSession()->getFlashBag()->add('success', 'Группа успешно создана.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
