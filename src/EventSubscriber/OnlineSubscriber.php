<?php

namespace App\EventSubscriber;

use App\Entity\Online;
use App\Repository\OnlineRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnlineSubscriber implements EventSubscriberInterface
{
    const UPDATE_TIME = 30 ;
    protected $online ;
    protected $onlineRepository ;
    protected $manager ;
    public function __construct(OnlineRepository $onlineRepository, ObjectManager $manager)
    {
        $this->online = new Online() ;
        $this->onlineRepository = $onlineRepository ;

        $this->manager = $manager ;
    }

    public function onKernelTerminate(TerminateEvent $event)
    {
        $request =  $event->getRequest();

        $ip = $request->getClientIp();

        $time = date('H:i:s',time() + self::UPDATE_TIME ) ;
        $sessionTime = new \DateTime( $time) ;
        //dump(date('g:i:s A', $sessionTime->getTimestamp() )) ;
        $currentTime = new \DateTime() ;

        $online = $this->manager->getRepository(Online::class)->findOneBy(['ipUser'=>$ip]) ;
        if ($online)
        {
            $online->setInsertedAt($currentTime) ;
            $this->manager->persist($online);
        } else {
            $this->online->setIpUser($ip) ;
            $this->online->setInsertedAt($currentTime);
            $this->manager->persist($this->online);
        }
        $this->manager->flush();

        $onlines = $this->manager->getRepository(Online::class)->findAll() ;

        if ($onlines)
        {
            $i = 0 ;
            
            $pastTime = time() + $currentTime->getTimestamp() - $sessionTime->getTimestamp() ;
            foreach ($onlines as $online )
            {
                if($online->getInsertedAt()->getTimestamp() < $pastTime)
                {
                    $this->manager->remove($online);
                    $i++ ;
                }

                //Si on a trop de requetes on flush
                if ($i >= 100)
                {
                    $this->manager->flush();
                }

            }
            $this->manager->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => ['onKernelTerminate', -100],
        ];
    }

}
