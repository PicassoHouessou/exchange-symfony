<?php


namespace App\Event;

use App\Entity\ContactUs;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactUsEvent
{
    public const SEND = 'contactUs.send';

    protected $contactUs;
    protected $user;

    public function __construct(ContactUs $contactUs, UserInterface $user = null)
    {
        $this->contactUs = $contactUs;
        $this->user = $user;
    }

    /**
     * @return ContactUs
     */
    public function getContactUs()
    {
        return $this->contactUs;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }


}