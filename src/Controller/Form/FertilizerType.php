<?php

namespace App\Controller\Form;

use App\Domain\Service\PlantService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\Model\Fertilizer\FertilizerModel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Fertilizer\EditFertilizer\Input\EditFertilizerDTO;
use App\Controller\Web\Dashboard\Fertilizer\CreateFertilizer\Input\CreateFertilizerDTO;

class FertilizerType extends AbstractType
{
    public function __construct(
        private readonly PlantService $plantService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = FertilizerModel::getTableHeaderRu();

        $builder
            ->add('plantId', ChoiceType::class, [
                'label' => $labels['plant_id'],
                'required' => true,
                'choices' => $this->plantService->getChoicesForChoiceType($groupId),
                'placeholder' => 'Выберите растение',
            ])
            ->add('title', TextType::class, [
                'label' => $labels['title'],
                'required' => false,
            ])
            ->add('quantity', TextType::class, [
                'label' => $labels['quantity'],
                'required' => false,
            ])
            ->add('letter', TextType::class, [
                'label' => $labels['letter'],
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
            'data_class' => EditFertilizerDTO::class,
            'empty_data' => new CreateFertilizerDTO(2, 2, '', 2, '', ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
