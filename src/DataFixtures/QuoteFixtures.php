<?php

namespace App\DataFixtures;

use App\Entity\BillingRow;
use App\Entity\Discount;
use App\Entity\Quote;
use App\Entity\Customer;
use App\Entity\QuoteDiscount;
use App\Entity\QuoteSignature;
use App\Entity\User;
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
            UserFixtures::class,
            CustomerFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $customers = $manager->getRepository(Customer::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $quote = new Quote();
            $quote->setEmitedAt($faker->dateTimeBetween('-1 years', 'now'));
            $quote->setExpiredAt($faker->dateTimeBetween('now', '+1 years'));
            $quote->setCustomer($customers[random_int(0, count($customers) - 1)]);
            $billingAddresses = $quote->getCustomer()->getBillingAddresses();
            if($billingAddresses->count() > 0){
                $quote->setBillingAddress($billingAddresses->first());
            }

            //billingRows
            $count = random_int(1,3);
            for($j = 0; $j < $count; $j++){
                $billingRow = new BillingRow();
                $billingRow->setQuoteId($quote);
                $billingRow->setUnit(random_int(10, 1000));
                $billingRow->setQuantity(random_int(1, 10));
                $billingRow->setProduct($faker->word);
                $billingRow->setVat(random_int(1, 20));

                if($faker->boolean(20)){
                    $discount = new Discount();
                    $discount->setType(random_int(1, 2));
                    $discount->setValue(random_int(1, 50));
                    $billingRow->setDiscount($discount);

                    $manager->persist($discount);
                }
                $manager->persist($billingRow);

            }

            $quote->setOwner($users[random_int(0, count($users) - 1)]);
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

            //global discount
            if($faker->boolean(20)){
                $discount = new Discount();
                $quoteDiscount = new QuoteDiscount();
                $discount->setType(1);
                $discount->setValue(random_int(1, 10));
                $quoteDiscount->setDiscount($discount);
                $quoteDiscount->setQuote($quote);
                $manager->persist($discount);
                $manager->persist($quoteDiscount);
            }

            $manager->persist($quote);
        }

        $manager->flush();
    }
}
