<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $currencyDollar = new Currency();
        $currencyEuro = new Currency() ;
        $currencyPound = new Currency() ;
        $currencyXOF = new Currency() ;

        $currencyDollar->setCode("USD")->setLabel("Dollar")->setReserve(10000000)->setRate(1);
        $currencyEuro->setCode("USD")->setLabel("Dollar")->setReserve(10000000)->setRate(1.079);
        $currencyPound->setCode("GBP")->setLabel("Livre")->setReserve(10000000)->setRate(1.3005);
        $currencyXOF->setCode("XOF")->setLabel("Francs Afrique de l'Ouest")->setReserve(1000000000)->setRate(0.0016);


        $manager->persist($currencyDollar);
        $manager->persist($currencyEuro);
        $manager->persist($currencyPound);
        $manager->persist($currencyXOF);
        $manager->flush();
    }
}
