<?php

namespace App\Controller\Form;

use App\Domain\Service\GroupService;
use Symfony\Component\Form\AbstractType;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Controller\Web\Dashboard\Marker\EditMarker\Input\EditMarkerDTO;
use App\Controller\Web\Dashboard\Marker\CreateMarker\Input\CreateMarkerDTO;

class MarkerType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly GroupService $groupService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $labels = MarkerModel::getTableHeaderRu();

        if ($this->security->isGranted(UserRole::ROLE_ADMIN->value)) {
            $builder->add('groupId', ChoiceType::class, [
                'label' => $labels['group_id'],
                'required' => true,
                'choices' => $this->groupService->getChoicesForFormChoiceType(),
                'placeholder' => 'form.marker.field.placeholder',
                'choice_translation_domain' => false
            ]);
        }

        $builder
            ->add('letter', TextType::class, [
                'label' => $labels['letter'],
                'required' => false,
            ])
            ->add('color', ColorType::class, [
                'label' => $labels['color'],
                'attr' => ['class' => 'marker-color'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => $labels['type'],
                'required' => true,
                'choices' => AttachableType::asSelectArray(),
                'placeholder' => 'form.marker.field.placeholder',
                'choice_translation_domain' => false
            ])
            ->add('description', TextType::class, [
                'label' => $labels['description'],
                'required' => false,
            ])
            ->add('colorDescription', TextType::class, [
                'label' => $labels['color_description'],
                'required' => false,
            ])
            ->setMethod($options['is_new'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditMarkerDTO::class,
            'empty_data' => new CreateMarkerDTO(0, '', '', '', '', ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier'
        ]);
    }
}
