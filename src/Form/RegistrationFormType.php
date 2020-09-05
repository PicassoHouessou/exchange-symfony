<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserInfo;
use PhpParser\Node\Expr\BinaryOp\Equal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\SequentiallyValidator;

class RegistrationFormType extends AbstractType
{
    protected $propertyAccessor ;
    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor() ;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('gender', ChoiceType::class, [
                'choices'               => [
                    'Homme'             => UserInfo::GENDER_MALE,
                    'Femme'             => UserInfo::GENDER_FEMALE,
                ],
                'property_path'    => 'info.gender',
            ])
            ->add('firstName', null,[
                //'mapped'        => false,
                'property_path'    => 'info.first_name',
                'constraints'   => [
                    new Sequentially([
                        new NotNull(),
                        new NotBlank(),
                        new Length([
                            'max'   => 255,
                        ]),
                    ]),
                ]
                //'validation_groups'     => 'registration'
            ])
            ->add('lastName', null, [
                'property_path'    => 'info.last_name',
                'constraints'   => [
                    new Sequentially([
                        new NotNull(),
                        new NotBlank(),
                        new Length([
                            'max'   => 255,
                        ]),
                    ]),
                ]
                //'validation_groups'     => 'registration'
            ])
            ->add('agreeNewsletters', CheckboxType::class, [
                'mapped' => false,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('agreeRisks', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => "You should agree with risks",
                    ]),
                ],
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPlainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_registration_form_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => '_registration_form_token',
        ]);
    }
}
