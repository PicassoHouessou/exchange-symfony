<?php


namespace App\Event;


use App\Entity\Notification;

class NotificationEvent
{
    const NOTIFICATION_ADD = "notification.add";

    protected $notification ;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification ;
    }

    /**
     * @return Notification
     */
    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    /**
     * @param Notification $notification
     */
    public function setNotification(?Notification $notification): void
    {
        $this->notification = $notification;
    }

}