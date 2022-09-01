<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', Type\TextType::class, [
                'attr' => [
                    'placeholder' => 'forms.user.firstName'
                ]
            ])
            ->add('lastName', Type\TextType::class, [
                'attr' => [
                    'placeholder' => 'forms.user.lastName'
                ]
            ])
            ->add('email', Type\EmailType::class, [
                'attr' => [
                    'placeholder' => 'forms.user.email'
                ]
            ])
            ->add('password', Type\PasswordType::class, [
                'attr' => [
                    'placeholder' => 'forms.user.password'
                ]
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => 'sing.up'
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