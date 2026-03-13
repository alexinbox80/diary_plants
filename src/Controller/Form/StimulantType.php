<?php

namespace App\Controller\Form;

use App\Domain\Service\MarkerService;
use Symfony\Component\Form\AbstractType;
use App\Domain\Model\Stimulant\StimulantModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Stimulant\EditStimulant\Input\EditStimulantDTO;
use App\Controller\Web\Dashboard\Stimulant\CreateStimulant\Input\CreateStimulantDTO;

class StimulantType extends AbstractType
{
    public function __construct(
        private readonly MarkerService $markerService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = StimulantModel::getTableHeaderRu();

        $builder
            ->add('markerId', ChoiceType::class, [
                'label' => $labels['marker_id'],
                'required' => true,
                'choices' => $this->markerService->getChoicesForChoiceType($groupId, 'stimulant::class'),
                'placeholder' => 'Выберите сокращение',
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
            'data_class' => EditStimulantDTO::class,
            'empty_data' => new CreateStimulantDTO(2, 2, '', 2, '', ''),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
