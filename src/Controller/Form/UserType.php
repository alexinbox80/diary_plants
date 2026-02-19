<?php

namespace App\Controller\Form;

use App\Domain\Service\GroupService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\Model\User\UserModel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\User\EditUser\Input\EditUserDTO;
use App\Controller\Web\Dashboard\User\CreateUser\Input\CreateUserDTO;

class UserType extends AbstractType
{
    public function __construct(
        private readonly GroupService $groupService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = UserModel::getTableHeaderRu();

        $builder
//            ->add('plantId', ChoiceType::class, [
//                'label' => $labels['plant_id'],
//                'required' => true,
//                'choices' => $this->plantService->getChoicesForChoiceType($groupId),
//                'placeholder' => 'Выберите растение',
//            ])
            ->add('groupId', TextType::class, [
                'label' => $labels['group_id'],
                'required' => false,
            ])
//            ->add('title', TextType::class, [
//                'label' => $labels['title'],
//                'required' => false,
//            ])
//            ->add('quantity', TextType::class, [
//                'label' => $labels['quantity'],
//                'required' => false,
//            ])
//            ->add('letter', TextType::class, [
//                'label' => $labels['letter'],
//                'required' => false,
//            ])
//            ->add('manufacturer', TextType::class, [
//                'label' => $labels['manufacturer'],
//                'required' => false,
//            ])
//            ->add('description', TextareaType::class, [
//                'label' => $labels['description'],
//                'required' => false,
//            ])
//            ->add('comment', TextareaType::class, [
//                'label' => $labels['comment'],
//                'required' => false,
//            ])
            ->setMethod($options['is_new'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditUserDTO::class,
            'empty_data' => new CreateUserDTO(
                2,
                '',
                '',
                [],
                false,
                false,
                false,
                '',
                '',
                '',
                '',
                ''
            ),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
