<?php

namespace App\Controller\Web\Dashboard\Offspring\CreateOffspring;

use App\Controller\Form\OffspringType;
use App\Domain\Service\OffspringService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Offspring\CreateOffspring\Input\CreateOffspringDTO;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(OffspringType::class, null, ['is_new' => $isNew, 'group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateOffspringDTO $createOffspringDTO */
            $createOffspringDTO = $form->getData();

            if (!$createOffspringDTO->groupId) {
                $createOffspringDTO->groupId = $groupId;
            }

            $this->offspringService->createFromCreateOffspringDTO($createOffspringDTO);

            $message = $this->translator->trans('offspring.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);

            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
