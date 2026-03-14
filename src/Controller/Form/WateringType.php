<?php

namespace App\Controller\Form;

use App\Domain\Model\Watering\WateringModel;

use DateTimeImmutable;
use App\Domain\Service\MarkerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                'choices' => $this->markerService->getChoicesForChoiceType($groupId, 'watering::class'),
                'placeholder' => 'Выберите сокращение',
            ])
            ->add('amount', TextType::class, [
                'label' => $labels['amount'],
                'required' => false,
            ])
            ->add('wateringType', TextType::class, [
                'label' => $labels['watering_type'],
                'required' => false,
            ])
            ->add('wateringMethod', TextType::class, [
                'label' => $labels['watering_method'],
                'required' => false,
            ])
            ->add('wateredAt', DateType::class, [
                'label' => $labels['watered_at'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
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
            'empty_data' => new CreateWateringDTO(2, 2, '', 2, '', new DateTimeImmutable(), ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
