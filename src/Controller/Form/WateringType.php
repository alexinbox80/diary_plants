<?php

namespace App\Controller\Form;

use App\Domain\Model\Watering\WateringModel;

use App\Domain\Service\GroupService;
use App\Domain\Service\MarkerService;
use Symfony\Component\Form\AbstractType;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Watering\EditWatering\Input\EditWateringDTO;
use App\Controller\Web\Dashboard\Watering\CreateWatering\Input\CreateWateringDTO;

class WateringType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly GroupService $groupService,
        private readonly MarkerService $markerService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = WateringModel::getTableHeaderRu();

        if ($this->security->isGranted(UserRole::ROLE_ADMIN->value)) {
            $builder->add('groupId', ChoiceType::class, [
                'label' => $labels['group_id'],
                'required' => true,
                'choices' => $this->groupService->getChoicesForFormChoiceType(),
                'placeholder' => 'form.watering.field.placeholder',
                'choice_translation_domain' => false
            ]);

            $groupId = null;
        }

        $builder
            ->add('markerId', ChoiceType::class, [
                'label' => $labels['marker_id'],
                'required' => true,
                'choices' => $this->markerService->getChoicesForChoiceType($groupId, AttachableType::WATERING->value),
                'placeholder' => 'form.watering.field.marker_placeholder',
                'choice_translation_domain' => false
            ])
            ->add('amount', TextType::class, [
                'label' => $labels['amount'],
                'required' => false,
            ])
            ->add('waterType', ChoiceType::class, [
                'label' => $labels['water_type'],
                'required' => true,
                'choices' => WaterType::asSelectArray(),
                'placeholder' => 'form.watering.field.water_type_placeholder',
                'choice_translation_domain' => false
            ])
            ->add('wateringMethod', ChoiceType::class, [
                'label' => $labels['watering_method'],
                'required' => true,
                'choices' => WateringMethod::asSelectArray(),
                'placeholder' => 'form.watering.field.watering_method_placeholder',
                'choice_translation_domain' => false
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
            'empty_data' => new CreateWateringDTO(0, 0, 0, '', '', 0),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
