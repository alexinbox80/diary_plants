<?php

namespace App\Controller\Web\Dashboard\Group\EditGroup;

use App\Domain\Entity\Group;
use App\Controller\Form\GroupType;
use App\Domain\Service\GroupService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Group\EditGroup\Input\EditGroupDTO;

class Manager
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Group $group): array
    {
        $formData = new EditGroupDTO(
            $group->isActive(),
            $group->getTitle(),
            $group->getDescription()
        );

        $form = $this->formFactory->create(GroupType::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditGroupDTO $editGroupDTO */
            $editGroupDTO = $form->getData();

            $data = $request->request->all()['group'] ?? [];
            $editGroupDTO->isActive = (bool) ($data['isActive'] ?? false);

            $this->groupService->updateFromEditGroupDTO($group, $editGroupDTO);

            $request->getSession()->getFlashBag()->add('success', 'Группа успешно обновлена.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'group' => $group
        ];
    }
}
