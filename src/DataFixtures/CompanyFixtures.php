<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompanyFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 10; $i++) {
            $company = new Company();
            $company->setName($faker->company);
            $company->setSiret((string) $faker->numberBetween(1000000000000, 9999999999999));
            $company->setVatNumber((string) $faker->numberBetween(1000000000000, 9999999999999));
            $manager->persist($company);
        }
        
        $manager->flush();        
        $this->addReference("company", $company);
    }
}