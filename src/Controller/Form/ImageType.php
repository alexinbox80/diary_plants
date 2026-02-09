<?php

namespace App\Controller\Form;

use App\Controller\Web\Dashboard\Image\EditImage\Input\EditImageDTO;
use App\Controller\Web\Dashboard\Image\CreateImage\Input\CreateImageDTO;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\ValueObject\Enum\AttachableType;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $labels = AttachmentModel::getTableHeaderRu();
        $builder
            ->add('isShown', CheckboxType::class, [
                'label' => $labels['is_shown_label'],
                'required' => false,
                'attr' => ['title' => 'Отображать изображение на сайте'],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Изображение',
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
            ->add('fileDate', DateTimeType::class, [
                'data' => new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')),
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
                'placeholder' => 'Выбери тип изображения',
            ])
            ->setMethod($options['isNew'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditImageDTO::class,
            'empty_data' => fn() => new CreateImageDTO(
                false, '', '', '', '', '', new DateTimeImmutable(), null, 0, '', null
            ),
            'isNew' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'unique_form_identifier',
        ]);
    }
}
