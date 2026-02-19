<?php

namespace App\Controller\Form;

use App\Domain\Model\User\UserModel;
use App\Domain\Service\GroupService;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            ->add('avatarFile', FileType::class, [
                'label' => 'Аватар',
                'mapped' => true,
                'required' => false,
            ])
            ->add('avatarLink', TextType::class, [
                'label' => $labels['avatar_link'],
                'disabled' => true,
            ])
            ->add('groupId', ChoiceType::class, [
                'label' => $labels['group_id'],
                'required' => true,
                'choices' => $this->groupService->getChoicesForChoiceType($groupId),
                'placeholder' => 'Выберите группу',
            ])
            ->add('email', TextType::class, [
                'label' => $labels['email'],
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'label' => $labels['password'] ?? 'Пароль',
                'required' => false,
            ])
//            ->add('roles', TextType::class, [
//                'label' => $labels['roles'],
//                //'choices' => UserRole::getChoices(),
//                'data' => 'ROLE_MANAGER',
//                //'multiple' => false,
//            ])
            ->add('isActive', CheckboxType::class, [
                'label' => $labels['is_active'],
                'required' => false,
                'attr' => ['title' => 'Пользователь активен'],
            ])
            ->add('emailConfirmed', CheckboxType::class, [
                'label' => $labels['email_confirmed'],
                'required' => false,
                'attr' => ['title' => 'Электронная почта подтверждена'],
            ])
            ->add('phoneConfirmed', CheckboxType::class, [
                'label' => $labels['phone_confirmed'],
                'required' => false,
                'attr' => ['title' => 'Телефон подтвержден'],
            ])
            ->add('timeZone', TextType::class, [
                'label' => $labels['time_zone'],
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => $labels['last_name'],
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'label' => $labels['first_name'],
                'required' => false,
            ])
            ->add('middleName', TextType::class, [
                'label' => $labels['middle_name'],
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => $labels['phone'],
                'required' => false,
            ])
            ->add('phoneCode', TextType::class, [
                'label' => $labels['phone_code'],
                'required' => false,
            ])
            ->add('emailCode', TextType::class, [
                'label' => $labels['email_code'],
                'required' => false,
            ])
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
