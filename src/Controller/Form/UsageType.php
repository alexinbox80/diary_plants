<?php

namespace App\Controller\Form;

use DateTimeImmutable;
use App\Domain\Service\PlantService;
use App\Domain\Model\Usage\UsageModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Usage\EditUsage\Input\EditUsageDTO;
use App\Controller\Web\Dashboard\Usage\CreateUsage\Input\CreateUsageDTO;

class UsageType extends AbstractType
{
    public function __construct(
        private readonly PlantService $plantService,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groupId = $options['group_id'] ?? null;
        $labels = UsageModel::getTableHeaderRu();

        $builder
            ->add('plantId', ChoiceType::class, [
                'label' => $labels['plant_id'],
                'required' => true,
                'choices' => $this->plantService->getChoicesForChoiceType($groupId),
                'placeholder' => 'Выберите растение',
            ])
            ->add('useDate', DateType::class, [
                'label' => $labels['use_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
            ])
            ->add('usableId', TextType::class, [
                'label' => $labels['usable_id'],
                'required' => true
            ])
            ->add('usableType', ChoiceType::class, [
                'label' => $labels['usable_type'],
                'required' => true,
                'choices' => AttachableType::getChoices(),
                'placeholder' => 'Выбери тип',
            ])
            ->add('comment', TextareaType::class, [
                'label' => $labels['comment'],
                'required' => false
            ])
            ->setMethod($options['is_new'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditUsageDTO::class,
            'empty_data' => fn() => new CreateUsageDTO(
                2, 2, new DateTimeImmutable(), 2, '', null
            ),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
            'group_id' => null,
        ]);
    }
}
