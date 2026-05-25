<?php

namespace App\Controller\Web\Dashboard\Group\EditGroup;

use App\Domain\Entity\Group;
use App\Controller\Form\GroupType;
use App\Domain\Service\GroupService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Group\EditGroup\Input\EditGroupDTO;

final class Manager
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function editFormData(Request $request, Group $group): array
    {
        $formData = new EditGroupDTO(
            isActive: $group->isActive(),
            title: $group->getTitle(),
            description: $group->getDescription()
        );

        $form = $this->formFactory->create(GroupType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditGroupDTO $editGroupDTO */
            $editGroupDTO = $form->getData();

            $data = $request->request->all()['group'] ?? [];
            $editGroupDTO->isActive = (bool) ($data['isActive'] ?? false);

            $this->groupService->updateFromEditGroupDTO($group, $editGroupDTO);

            $message = $this->translator->trans('group.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);

            return ['success' => true];
        }

        return [
            'form' => $form,
            'group' => $group
        ];
    }
}
