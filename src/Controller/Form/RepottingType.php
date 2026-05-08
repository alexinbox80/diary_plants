<?php

namespace App\Controller\Form;

use DateTimeImmutable;
use App\Domain\Service\GroupService;
use App\Domain\Service\PlantService;
use Symfony\Component\Form\AbstractType;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use App\Domain\Model\Repotting\RepottingModel;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Repotting\EditRepotting\Input\EditRepottingDTO;
use App\Domain\ValueObject\Enum\Repotting\RepottingType as RepottingTypeChoices;
use App\Controller\Web\Dashboard\Repotting\CreateRepotting\Input\CreateRepottingDTO;

class RepottingType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly GroupService $groupService,
        private readonly PlantService $plantService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = RepottingModel::getTableHeaderRu();

        if ($this->security->isGranted(UserRole::ROLE_ADMIN->value)) {
            $builder->add('groupId', ChoiceType::class, [
                'label' => $labels['group_id'],
                'required' => true,
                'choices' => $this->groupService->getChoicesForFormChoiceType(),
                'placeholder' => 'Выберите группу'
            ]);

            $groupId = null;
        }

        $builder
            ->add('plantId', ChoiceType::class, [
                'label' => $labels['plant_title'],
                'required' => true,
                'choices' => $this->plantService->getChoicesForChoiceType($groupId),
                'placeholder' => 'Выберите растение',
            ])
            ->add('repottedAt', DateType::class, [
                'label' => $labels['repotted_at'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                'data' => new DateTimeImmutable(),
            ])
            ->add('type', ChoiceType::class, [
                'label' => $labels['type'],
                'choices' => RepottingTypeChoices::getChoices(),
                'placeholder' => 'Выберите тип пересадки'
            ])
            ->add('potMaterial', ChoiceType::class, [
                'label' => $labels['pot_material'],
                'choices' => PotMaterial::getChoices(),
                'placeholder' => 'Выберите материал горшка'
            ])
            ->add('potSize', TextType::class, [
                'label' => $labels['pot_size'],
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
            'data_class' => EditRepottingDTO::class,
            'empty_data' => new CreateRepottingDTO(2, 1, new DateTimeImmutable(), '', '', ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
