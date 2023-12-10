<?php

namespace App\DataFixtures;

use App\Entity\Quote;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class QuoteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $quote = new Quote();

        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $quote = new Quote();
            $quote->setTitle("quote_" . $quote->getID());
            $quote->setEmitedAt($faker->dateTimeBetween('-1 years', 'now'));
            $quote->setExpiredAt($faker->dateTimeBetween('now', '+1 years'));
            $quote->setHasBeenSigned(random_int(0, 1));
            $manager->persist($quote);
        }

        $manager->flush();
    }
}
