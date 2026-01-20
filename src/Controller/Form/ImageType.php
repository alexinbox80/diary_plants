<?php

namespace App\Controller\Form;

use App\Controller\Web\Admin\Image\EditImage\Input\EditImageDTO;
use App\Controller\Web\Admin\Image\CreateImage\Input\CreateImageDTO;
use App\Domain\Model\Attachment\AttachmentModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('filename', TextType::class, [
                'label' => $labels['filename'],
                'required' => true
            ])
            ->add('path', TextType::class, [
                'label' => $labels['path'],
                'required' => true
            ])
            ->add('mimeType', TextType::class, [
                'label' => $labels['mime_type'],
                'required' => true
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
                'label' => $labels['file_date'],
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('attachableId', TextareaType::class, [
                'label' => $labels['attachable_id'],
                'required' => true
            ])
            ->add('attachableType', TextareaType::class, [
                'label' => $labels['attachable_type'],
                'required' => true
            ])
            ->setMethod($options['isNew'] ? 'POST' : 'PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditImageDTO::class,
            'empty_data' => new CreateImageDTO(),
            'isNew' => false,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'unique_form_identifier',
        ]);
    }
}
