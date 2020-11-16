<?php

namespace App\EventSubscriber;

use App\Entity\UserInfo;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    protected $cacheManager ;
    protected $uploaderHelper ;
    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager ;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate',
        ];
    }

    public function preRemove( LifecycleEventArgs $args )
    {
        $entity = $args->getEntity();

        // S'il s'agit des informations d'un utilisateur
        if ($entity instanceof UserInfo) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'avatarFile','App\\Entity\\UserInfo') ) ;

        }

    }

    public function preUpdate(PreUpdateEventArgs $args){
        $entity = $args->getEntity() ;

        if ( $entity instanceof UserInfo)
        {
            if ($entity->getAvatarFile() instanceof UploadedFile) {
                $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'avatarFile','App\\Entity\\UserInfo') ) ;
            }
        }


    }
}
