<?php

namespace App\Controller\Form;

use App\Domain\Model\Watering\WateringModel;

use DateTimeImmutable;
use App\Domain\Service\MarkerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Watering\EditWatering\Input\EditWateringDTO;
use App\Controller\Web\Dashboard\Watering\CreateWatering\Input\CreateWateringDTO;

class WateringType extends AbstractType
{
    public function __construct(
        private readonly MarkerService $markerService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = WateringModel::getTableHeaderRu();

        $builder
            ->add('markerId', ChoiceType::class, [
                'label' => $labels['marker_id'],
                'required' => true,
                'choices' => $this->markerService->getChoicesForChoiceType($groupId, AttachableType::WATERING->value),
                'placeholder' => 'Выберите сокращение',
            ])
            ->add('amount', TextType::class, [
                'label' => $labels['amount'],
                'required' => false,
            ])
            ->add('waterType', ChoiceType::class, [
                'label' => $labels['water_type'],
                'required' => true,
                'choices' => WaterType::asSelectArray(),
                'placeholder' => 'Выберите сокращение',
            ])
            ->add('wateringMethod', ChoiceType::class, [
                'label' => $labels['watering_method'],
                'required' => true,
                'choices' => WateringMethod::asSelectArray(),
                'placeholder' => 'Выберите сокращение',
            ])
            ->add('temperature', TextType::class, [
                'label' => $labels['temperature'],
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => $labels['description'],
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => $labels['comment'],
                'required' => false,
            ])
            ->setMethod($options['is_new'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditWateringDTO::class,
            'empty_data' => new CreateWateringDTO(2, 2, 0, '', '', 0),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
