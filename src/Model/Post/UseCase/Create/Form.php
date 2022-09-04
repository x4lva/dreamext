<?php

declare(strict_types=1);

namespace App\Model\Post\UseCase\Create;

use App\Model\Post\Entity\PostTranslation;
use App\Model\Post\Form\PostTranslationType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', Type\CollectionType::class, [
                'entry_type' => PostTranslationType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'data' => [new PostTranslation()],
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Min'
                    ])
                ]
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'forms.post.save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}