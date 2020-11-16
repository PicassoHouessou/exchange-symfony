<?php

namespace App\Form;

use App\Entity\ContactUs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactUsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject')
            ->add('message')
            ->add('email')
            //->add('sentAt')
            ->add('sender')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactUs::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'contact_us_token',
            'csrf_token_id'   => 'contact_us_token',
        ]);
    }
}
