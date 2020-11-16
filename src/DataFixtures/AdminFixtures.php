<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixtures extends Fixture
{
    protected  $passwordEncoder ;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder ;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new Admin();
        $admin->setEmail('houessoupicasso@yahoo.fr');
        $admin->setCreatedAt(new \DateTime());
        $admin->setPassword($this->passwordEncoder->encodePassword($admin,'isidore0123')) ;

        $manager->persist($admin);

        $admin2 = new Admin();
        $admin2->setEmail('admin@admin.admin');
        $admin2->setCreatedAt(new \DateTime());
        $admin2->setPassword($this->passwordEncoder->encodePassword($admin2, 'adminadmin')) ;
        $manager->persist($admin2);
        $manager->flush();
    }
}
