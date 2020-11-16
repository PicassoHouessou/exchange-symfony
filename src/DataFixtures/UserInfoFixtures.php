<?php

namespace App\DataFixtures;

use App\Entity\Profession;
use App\Entity\User;
use App\Entity\UserInfo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserInfoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $profession = new Profession() ;
        $profession->setName('Mécanicien')  ;

        for ($i = 0 ; $i<500 ; $i++){

            $email = 'test'.$i .'test@.fr';
            $user =  $manager->getRepository(User::class)->findOneBy(['email' => $email ]);
            $userInfo = new UserInfo() ;
            $userInfo
                ->setUser($user)
                ->setGender(UserInfo::GENDER_MALE)
                ->setMainActivity('project')
                ->setLastName('ZZZ')
                ->setFirstName('ffff')
                ->setPhoneNumber(394848585)
                ->addProfession( $profession)
                ->setBirthday(new \DateTime())
                ->setCity('Cotonou')
                ->setCountry('Benin');

            $manager->persist($userInfo);
        }
        $manager->flush();
    }
}
