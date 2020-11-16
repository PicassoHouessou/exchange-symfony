<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected  $passwordEncoder ;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder ;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User() ;
        $password =  $this->passwordEncoder->encodePassword($user, 'iiiiiiii') ;

        for ($i =0 ; $i<5 ; $i++)
        {
            $user = new User() ;
            $email = 'test'.$i .'test@.fr';

            $user->setEmail($email) ;
            $user->setPassword($password) ;
            $user->setIsEnabled(true);
            $user->setCreatedAt(new \DateTime());

            $manager->persist($user);
        }
        $manager->flush();

    }
}
