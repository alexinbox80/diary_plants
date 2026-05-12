<?php

namespace App\Controller\Web\Dashboard\Pest\CreatePest;

use App\Controller\Form\PestType;
use App\Domain\Service\PestService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Pest\CreatePest\Input\CreatePestDTO;

class Manager
{
    public function __construct(
        private readonly PestService $pestService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(PestType::class, null, ['is_new' => $isNew, 'group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreatePestDTO $createPestDTO */
            $createPestDTO = $form->getData();

            if (!$createPestDTO->groupId) {
                $createPestDTO->groupId = $groupId;
            }

            $this->pestService->createFromCreatePestDTO($createPestDTO);

            $message = $this->translator->trans('pest.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
