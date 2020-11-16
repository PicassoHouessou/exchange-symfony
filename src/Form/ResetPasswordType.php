<?php

namespace App\Form;

use App\Form\Model\ResetPasswordFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Password',
                    'class'       => 'form-control',
                ],
            ])
            ->add('confirmPlainPassword', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Confirm Password',
                    'class'       => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ResetPasswordFormModel::class,
        ]);
    }
}
