<?php

namespace App\Controller\Form;

use App\Domain\Model\Pest\PestModel;
use App\Domain\Service\GroupService;
use App\Domain\Service\MarkerService;
use Symfony\Component\Form\AbstractType;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Pest\EditPest\Input\EditPestDTO;
use App\Controller\Web\Dashboard\Pest\CreatePest\Input\CreatePestDTO;

class PestType extends AbstractType
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
        $labels = PestModel::getTableHeaderRu();

        if ($this->security->isGranted(UserRole::ROLE_ADMIN->value)) {
            $builder->add('groupId', ChoiceType::class, [
                'label' => $labels['group_id'],
                'required' => true,
                'choices' => $this->groupService->getChoicesForFormChoiceType(),
                'placeholder' => 'form.pest.field.placeholder',
                'choice_translation_domain' => false
            ]);

            $groupId = null;
        }

        $builder
            ->add('markerId', ChoiceType::class, [
                'label' => $labels['marker_id'],
                'required' => true,
                'choices' => $this->markerService->getChoicesForChoiceType($groupId, AttachableType::PEST->value),
                'placeholder' => 'form.pest.field.marker_placeholder',
                'choice_translation_domain' => false
            ])
            ->add('title', TextType::class, [
                'label' => $labels['title'],
                'required' => false,
            ])
            ->add('amount', TextType::class, [
                'label' => $labels['amount'],
                'required' => false,
            ])
            ->add('applicationRate', TextType::class, [
                'label' => $labels['application_rate'],
                'required' => false,
            ])
            ->add('manufacturer', TextType::class, [
                'label' => $labels['manufacturer'],
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
            'data_class' => EditPestDTO::class,
            'empty_data' => new CreatePestDTO(0, 0, '', 0, '', ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
