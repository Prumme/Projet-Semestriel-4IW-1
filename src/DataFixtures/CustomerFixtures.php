<?php

namespace App\DataFixtures;

use App\Entity\BillingAddress;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies() : array
    {
        return [
            CompanyFixtures::class,
            UserFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        for($i = 0; $i < 10; $i++){
            $customer = new Customer(); 
            $customer->setLastname($faker->lastName);
            $customer->setFirstname($faker->firstName);
            $customer->setCompanyName($faker->company);
            $customer->setCompanySiret((string) $faker->numberBetween(1000000000000, 9999999999999));
            $customer->setCompanyVatNumber((string) $faker->numberBetween(1000000000000, 9999999999999));
            $customer->setTel($faker->phoneNumber);
            $customer->setReferenceCompany($this->getReference("company"));
            $this->createBillingAddress($manager, $customer);
            $manager->persist($customer);
        }
        $manager->flush();
    }

    public function createBillingAddress($manager, Customer $customer){
        $faker = \Faker\Factory::create();
        $billingAddress = new BillingAddress($manager);
        $billingAddress->setCity($faker->city);
        $billingAddress->setZipCode((string) $faker->numberBetween(10000, 99999));
        $billingAddress->setCountryCode($faker->countryCode);
        $billingAddress->setAddressLine1($faker->streetAddress);

        $random = rand(0, 1);
        if($random == 1) $billingAddress->setAddressLine2($faker->streetAddress);

        $billingAddress->setCustomer($customer);

        $manager->persist($billingAddress);
    }
}
