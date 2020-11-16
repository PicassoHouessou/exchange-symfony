<?php


namespace App\Event;

use App\Entity\Newsletters;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Service\ServiceSubscriberInterface;


class NewslettersEvent extends Event
{
    const NEWSLETTERS_ADD = 'newsletters.add';
    protected $newsletters;

    public function __construct(Newsletters $newsletters)
    {
        $this->newsletters = $newsletters ;
    }

    /**
     * @return Newsletters
     */
    public function getNewsletters(): Newsletters
    {
        return $this->newsletters;
    }


}