<?php


namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{

    public const NEW_USER = 'user.add' ;
    public const PROJECT_INVEST = 'user.invest' ;

    protected $user ;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}