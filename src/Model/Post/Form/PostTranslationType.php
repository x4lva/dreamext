<?php

declare(strict_types=1);

namespace App\Model\Post\Form;

use App\Model\Post\Entity\PostTranslation;
use App\Service\LocaleHelper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostTranslationType extends AbstractType
{
    private $localeHelper;

    public function __construct(LocaleHelper $localeHelper)
    {
        $this->localeHelper = $localeHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('languageCode', Type\ChoiceType::class, [
                'label' => 'forms.post.language',
                'choices' => $this->localeHelper->getLocalesAsChoices()
            ])
            ->add('title', Type\TextType::class, [
                'label' => 'forms.post.title'
            ])
            ->add('slug', Type\TextType::class, [
                'label' => 'forms.post.slug',
                'help' => 'forms.post.help.slug'
            ])
            ->add('content', CKEditorType::class, [
                'attr' => ['class' => 'content-editor'],
                'label' => 'forms.post.content',
                'empty_data' => ''
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'forms.post.image'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostTranslation::class,
        ]);
    }
}
