<?php

namespace App\Controller\Form;

use Symfony\Component\Form\AbstractType;
use App\Domain\Model\Marker\MarkerModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Marker\EditMarker\Input\EditMarkerDTO;
use App\Controller\Web\Dashboard\Marker\CreateMarker\Input\CreateMarkerDTO;

class MarkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = MarkerModel::getTableHeaderRu();

        $builder
            ->add('letter', TextType::class, [
                'label' => $labels['letter'],
                'required' => false,
            ])
            ->add('color', TextType::class, [
                'label' => $labels['color'],
                'required' => false,
            ])
            ->add('type', TextType::class, [
                'label' => $labels['type'],
                'required' => false,
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
            'empty_data' => new CreateMarkerDTO(2, '', '', '', '', ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
