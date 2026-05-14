<?php

namespace App\Controller\Form;

use DateTimeImmutable;
use App\Domain\Service\GroupService;
use Symfony\Component\Form\AbstractType;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use App\Domain\Model\Attachment\AttachmentModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Controller\Web\Dashboard\Image\EditImage\Input\EditImageDTO;
use App\Controller\Web\Dashboard\Image\CreateImage\Input\CreateImageDTO;

class ImageType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly GroupService $groupService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $labels = AttachmentModel::getTableHeaderRu();

        if ($this->security->isGranted(UserRole::ROLE_ADMIN->value)) {
            $builder->add('groupId', ChoiceType::class, [
                'label' => $labels['group_id'],
                'required' => true,
                'choices' => $this->groupService->getChoicesForFormChoiceType(),
                'placeholder' => 'form.attachment.field.placeholder',
                'choice_translation_domain' => false
            ]);
        }

        $builder
            ->add('isShown', CheckboxType::class, [
                'label' => $labels['is_shown_label'],
                'required' => false,
                'attr' => ['title' => 'form.attachment.field.is_shown_label'],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'form.attachment.field.image_file_label',
                'mapped' => true,
                'required' => false,
            ])
            ->add('filename', TextType::class, [
                'label' => $labels['filename'],
                'disabled' => true,
            ])
            ->add('path', TextType::class, [
                'label' => $labels['path'],
                'disabled' => true
            ])
            ->add('mimeType', TextType::class, [
                'label' => $labels['mime_type'],
                'disabled' => true
            ])
            ->add('alt', TextType::class, [
                'label' => $labels['alt'],
                'required' => true
            ])
            ->add('title', TextType::class, [
                'label' => $labels['title'],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => $labels['description'],
                'required' => true
            ])
            ->add('fileDate', DateType::class, [
                'data' => new DateTimeImmutable('now'),
                'label' => $labels['file_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('attachableId', TextType::class, [
                'label' => $labels['attachable_id'],
                'required' => true
            ])
            ->add('attachableType', ChoiceType::class, [
                'label' => $labels['attachable_type'],
                'required' => true,
                'choices' => AttachableType::getChoices(),
                'placeholder' => 'form.attachment.field.attachable_type_label',
                'choice_translation_domain' => false
            ])
            ->setMethod($options['is_new'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditImageDTO::class,
            'empty_data' => fn() => new CreateImageDTO(
                0, false, '', '', '', '', '', new DateTimeImmutable(), null, 0, '', null
            ),
            'is_new' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
        ]);
    }
}
