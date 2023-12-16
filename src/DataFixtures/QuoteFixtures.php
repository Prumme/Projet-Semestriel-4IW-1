<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use App\DataFixtures\CustomerFixtures;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class QuoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private CustomerRepository $customerRepo)
    {
    }

    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $quote = new Quote();
        $customers = $manager->getRepository(Customer::class)->findAll();

        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $quote = new Quote();
            $quote->setEmitedAt($faker->dateTimeBetween('-1 years', 'now'));
            $quote->setExpiredAt($faker->dateTimeBetween('now', '+1 years'));
            $quote->setHasBeenSigned(random_int(0, 1));
            $quote->setCustomer($customers[random_int(0, count($customers) - 1)]);
            $manager->persist($quote);
        }

        $manager->flush();
    }
}
