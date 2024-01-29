<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use App\DataFixtures\CustomerFixtures;
use App\Entity\Customer;
use App\Entity\QuoteSignature;
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
            $quote->setCustomer($customers[random_int(0, count($customers) - 1)]);
            $hasSignature = random_int(0, 1);
            if ($hasSignature) {
                $signature = new QuoteSignature();
                $signature->setDataBase64URI($faker->imageUrl(100, 100, 'cats', true, 'Faker', true));
                $signature->setSignedBy($faker->name());
                $date = $faker->dateTimeBetween('-1 years', 'now');
                $dateImmutable = new \DateTimeImmutable();
                $dateImmutable->setTimestamp($date->getTimestamp());
                $signature->setSignedAt($dateImmutable);
                $signature->setHasBeenAgreed(true);
                $quote->setSignature($signature);
            }
            $manager->persist($quote);
        }

        $manager->flush();
    }
}
