<?php

namespace App\DataFixtures;

use App\Entity\ContactUs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContactUsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=0 ; $i <500 ; $i++)
        {
            $contactUs = new ContactUs();
            $contactUs
                ->setEmail('houeee'.$i.'eee@.fr')
                ->setCreatedAt(new \DateTime() )
                ->setSender("Caleta ".$i)
                ->setSubject("Parrainage de l'evernement ". $i)
                ->setMessage("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 ")
                ;
            $manager->persist($contactUs);

        }

        $manager->flush();
    }
}
