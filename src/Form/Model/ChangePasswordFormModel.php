<?php


namespace App\Form\Model;


use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFormModel
{
    /**
     *
     * @Assert\Sequentially(
     *     @Assert\NotNull(),
     *     @Assert\Length(max="255")
     * )
     */
    public $oldPlainPassword;

    /**
     *
     * @Assert\Sequentially(
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="255", min="8"),
     *     @Assert\Regex("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#", message="Your password must content a tiny, a capital letter, a special character and a number. Ex: aZ222@Xwj")
     * )
     *
     */
    public $newPlainPassword ;

    /**
     * @Assert\Sequentially(
     *     @Assert\EqualTo(propertyPath="newPlainPassword", message="Passwords are not same")
     * )
     */
    public $confirmPlainPassword ;

}