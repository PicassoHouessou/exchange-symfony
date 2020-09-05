<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Model\ChangePasswordFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPlainPassword', PasswordType::class)
            ->add('newPlainPassword', PasswordType::class)
            ->add('confirmPlainPassword', PasswordType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePasswordFormModel::class,
        ]);
    }
}
