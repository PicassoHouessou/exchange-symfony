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
        $admin2 = new Admin();
        $admin2->setEmail('admin@admin.com');
        $admin2->setCreatedAt(new \DateTime());
        $admin2->setPassword($this->passwordEncoder->encodePassword($admin2, 'admin')) ;
        $admin2->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin2);
        $manager->flush();
    }
}
