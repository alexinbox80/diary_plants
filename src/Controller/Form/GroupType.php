<?php

namespace App\Controller\Form;

use App\Domain\Model\Group\GroupModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Controller\Web\Dashboard\Group\EditGroup\Input\EditGroupDTO;
use App\Controller\Web\Dashboard\Group\CreateGroup\Input\CreateGroupDTO;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = GroupModel::getTableHeaderRu();

        $builder
            ->add('isActive', CheckboxType::class, [
                'label' => $labels['is_active'],
                'required' => false,
                'attr' => ['title' => 'Отключить пользователя'],
            ])
            ->add('title', TextType::class, [
                'label' => $labels['title'],
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => $labels['description'],
                'required' => false,
            ])
            ->setMethod($options['is_new'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditGroupDTO::class,
            'empty_data' => new CreateGroupDTO(false, ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
