<?php

namespace App\Controller\Form;

use DateTimeImmutable;
use App\Domain\Service\GroupService;
use App\Domain\Model\Plant\PlantModel;
use Symfony\Component\Form\AbstractType;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Controller\Web\Dashboard\Plant\EditPlant\Input\EditPlantDTO;
use App\Controller\Web\Dashboard\Plant\CreatePlant\Input\CreatePlantDTO;

class PlantType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly GroupService $groupService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $labels = PlantModel::getTableHeaderRu();

        if ($this->security->isGranted(UserRole::ROLE_ADMIN->value)) {
            $builder->add('groupId', ChoiceType::class, [
                'label' => $labels['group_id'],
                'required' => true,
                'choices' => $this->groupService->getChoicesForFormChoiceType(),
                'placeholder' => 'form.plant.field.placeholder',
                'choice_translation_domain' => false
            ]);
        }

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
                'data' => $options['is_new'] ? new DateTimeImmutable() : $builder->getData()->purchaseDate,
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
                'attr' => ['title' => 'form.plant.field.price'],
            ])
            ->add('shippingCost', TextType::class, [
                'label' => $labels['shipping_cost'],
                'required' => false,
                'attr' => ['title' => 'form.plant.field.price'],
            ])
            ->add('packagingCost', TextType::class, [
                'label' => $labels['packaging_cost'],
                'required' => false,
                'attr' => ['title' => 'form.plant.field.price'],
            ])
            ->add('isShown', CheckboxType::class, [
                'label' => $labels['is_shown'],
                'required' => false,
                'attr' => ['title' => 'form.plant.field.is_shown'],
            ])
            ->add('soil', TextType::class, [
                'label' => $labels['soil'],
                'required' => false,
            ])
            ->add('isSold', CheckboxType::class, [
                'label' => $labels['is_sold'],
                'required' => false,
                'attr' => ['title' => 'form.plant.field.is_sold'],
            ])
            ->add('sellingDate', DateType::class, [
                'label' => $labels['selling_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
            ])
            ->add('sellingPrice', TextType::class, [
                'label' => $labels['selling_price'],
                'required' => false,
                'attr' => ['title' => 'form.plant.field.price'],
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
            'data_class' => EditPlantDTO::class,
            'empty_data' => new CreatePlantDTO(),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
        ]);
    }
}
