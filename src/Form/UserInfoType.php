<?php

namespace App\Form;

use App\Entity\Profession;
use App\Entity\UserInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarFile', FileType::class)
            ->add('phoneNumber')
            ->add('country', CountryType::class)
            ->add('city')
            ->add('hasCompany')
            ->add('professions', EntityType::class, [
                'multiple'      => true,
                'class'       => Profession::class,
                'choice_label'  => function ($category) {
                    return $category->getName();
                },
                'attr' => ['class' => 'select-js'],
            ])
            ->add('mainActivity',ChoiceType::class,[
                'choices'   =>[
                    'Porteur de projet'                     =>  'entrepenor',
                    'Investir dans les projets porteurs'    =>  'investissor',
                ],
                'expanded'   => true

                // 'choice_attr' => function($choice, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                //return ['class' => 'attending_'.strtolower($key)];
                //}
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInfo::class,
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => '_token',
        ]);
    }
}
