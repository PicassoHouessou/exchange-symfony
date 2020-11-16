<?php

namespace App\DataFixtures;

use App\Entity\Newsletters;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NewslettersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for( $i= 0 ; $i< 5 ; $i++)
        {
            $newsletters = new Newsletters();
            $email = 'test'.$i .'test@.fr';
            $newsletters
                ->setEmail($email)
                ->setCreatedAt(new \DateTime());

            $manager->persist($newsletters);
        }

        $manager->flush();
    }
}
