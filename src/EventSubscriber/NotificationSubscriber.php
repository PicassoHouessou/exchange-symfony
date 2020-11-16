<?php

namespace App\EventSubscriber;

use App\Event\NotificationEvent;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber implements EventSubscriberInterface
{
    protected $manager ;
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager ;

    }

    public function onNotificationAdd($event)
    {
        if ($event instanceof NotificationEvent)
        {
            $this->manager->persist($event->getNotification());
            $this->manager->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            NotificationEvent::NOTIFICATION_ADD => 'onNotificationAdd',
        ];
    }
}
