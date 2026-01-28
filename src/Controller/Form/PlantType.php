<?php

namespace App\Controller\Form;

use App\Controller\Web\Dashboard\Plant\EditPlant\Input\EditPlantDTO;
use App\Controller\Web\Dashboard\Plant\CreatePlant\Input\CreatePlantDTO;
use App\Domain\Model\Plant\PlantModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $labels = PlantModel::getTableHeaderRu();

        $builder
            ->add('title', TextType::class, [
                'label' => $labels['title'],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => $labels['description'],
                'required' => false,
            ])
            ->add('room', TextType::class, [
                'label' => $labels['room'],
                'required' => true,
            ])
            ->add('purchaseDate', DateType::class, [
                'label' => $labels['purchase_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
            ])
            ->add('vaccinationDate', DateType::class, [
                'label' => $labels['vaccination_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
            ])
            ->add('plantingDate', DateType::class, [
                'label' => $labels['planting_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
            ])
            ->add('seller', TextType::class, [
                'label' => $labels['seller'],
                'required' => false,
            ])
            ->add('nursery', TextType::class, [
                'label' => $labels['nursery'],
                'required' => false,
            ])
            ->add('price', TextType::class, [
                'label' => $labels['price'],
                'required' => false,
                'attr' => ['title' => 'Формат: "100.00 RUB"'],
            ])
            ->add('shippingCost', TextType::class, [
                'label' => $labels['shipping_cost'],
                'required' => false,
                'attr' => ['title' => 'Формат: "100.00 RUB"'],
            ])
            ->add('packagingCost', TextType::class, [
                'label' => $labels['packaging_cost'],
                'required' => false,
                'attr' => ['title' => 'Формат: "100.00 RUB"'],
            ])
            ->add('isShown', CheckboxType::class, [
                'label' => $labels['is_shown'],
                'required' => false,
                'attr' => ['title' => 'Отображать растение на сайте'],
            ])
            ->add('soil', TextType::class, [
                'label' => $labels['soil'],
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
            'data_class' => EditPlantDTO::class,
            'empty_data' => new CreatePlantDTO(),
            'isNew' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
        ]);
    }
}
