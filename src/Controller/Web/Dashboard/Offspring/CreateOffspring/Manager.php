<?php

namespace App\Controller\Web\Dashboard\Offspring\CreateOffspring;

use App\Controller\Web\Dashboard\Offspring\CreateOffspring\Input\CreateOffspringDTO;
use App\Domain\Model\Offspring\CreateOffspringModel;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\OffspringService;
use App\Controller\Form\OffspringType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly FormFactoryInterface $formFactory,
        private readonly ModelFactory $modelFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(OffspringType::class, null, ['isNew' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateOffspringDTO $createOffspringDTO */
            $createOffspringDTO = $form->getData();

            $createOffspringModel = $this->modelFactory->makeModel(
                CreateOffspringModel::class,
                $createOffspringDTO->plantId,
                $createOffspringDTO->fruitingDate,
                $createOffspringDTO->floweringDate,
                (int) $createOffspringDTO->mass,
                $createOffspringDTO->color,
                $createOffspringDTO->flavor,
                (int) $createOffspringDTO->quantity,
                $createOffspringDTO->comment,
            );

            $offspringModel = $this->offspringService->create($createOffspringModel);

            $request->getSession()->getFlashBag()->add('success', 'Плод успешно создан.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'isNew' => $isNew,
        ];
    }
}
