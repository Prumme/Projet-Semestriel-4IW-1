<?php

namespace App\DataFixtures;

use App\Entity\BillingAddress;
use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $company = new Company();
        $company->setName($faker->company);
        $company->setSiret((string) $faker->numberBetween(1000000000000, 9999999999999));
        $company->setVatNumber((string) $faker->numberBetween(1000000000000, 9999999999999));

        $address = new BillingAddress($manager);
        $address->setCity($faker->city);
        $address->setZipCode((int) $faker->postcode);
        $address->setCountryCode($faker->countryCode);
        $address->setAddressLine1($faker->streetAddress);
        if($faker->boolean(20)) {
            $address->setAddressLine2($faker->streetAddress);
        }
        $company->setAddress($address);
        $manager->persist($company);
        $manager->persist($address);
        $manager->flush();
        
        $this->addReference("company", $company);
    }
}
