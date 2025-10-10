<?php

namespace App\Controller\Form;

use App\Controller\Web\Admin\Offspring\EditOffspring\Input\EditOffspringDTO;
use App\Controller\Web\Admin\Offspring\CreateOffspring\Input\CreateOffspringDTO;
use App\Domain\Model\Offspring\OffspringModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffspringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $labels = OffspringModel::getTableHeaderRu();

        $builder
            ->add('plantId', TextType::class, [
                'label' => $labels['plant_id'],
                'required' => true
            ])
            ->add('fruitingDate', DateTimeType::class, [
                'label' => $labels['fruiting_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('floweringDate', DateTimeType::class, [
                'label' => $labels['flowering_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('mass', TextType::class, [
                'label' => $labels['mass'],
                'required' => false,
            ])
            ->add('color', TextType::class, [
                'label' => $labels['color'],
                'required' => false,
            ])
            ->add('flavor', TextType::class, [
                'label' => $labels['flavor'],
                'required' => false,
            ])
            ->add('quantity', TextType::class, [
                'label' => $labels['quantity'],
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => $labels['comment'],
                'required' => false,
            ])
            ->setMethod($options['isNew'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditOffspringDTO::class,
            'empty_data' => new CreateOffspringDTO(),
            'isNew' => false,
        ]);
    }
}
