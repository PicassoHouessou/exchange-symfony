<?php


namespace App\Form\Model;


use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordFormModel
{
    #[Assert\NotNull(message: 'Please enter a password')]
    public $plainPassword;

    /**
     * @Assert\Sequentially(
     *     @Assert\NotNull(),
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="255"),
     *     @Assert\EqualTo(propertyPath="plainPassword")
     * )
     */
    public $confirmPlainPassword ;

}