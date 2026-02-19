<?php

namespace App\Controller\Form;

use Symfony\Component\Form\AbstractType;
use App\Domain\Model\Offspring\OffspringModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Offspring\EditOffspring\Input\EditOffspringDTO;
use App\Controller\Web\Dashboard\Offspring\CreateOffspring\Input\CreateOffspringDTO;

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
            ->add('fruitingDate', DateType::class, [
                'label' => $labels['fruiting_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
            ])
            ->add('floweringDate', DateType::class, [
                'label' => $labels['flowering_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
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
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
        ]);
    }
}
