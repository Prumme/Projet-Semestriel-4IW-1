<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Price;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PriceFixtures extends Fixture implements DependentFixtureInterface{

    public function getDependencies() : array
    {
        return [
            ProductFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void{
        
        $products = $manager->getRepository(Product::class)->findAll();
        foreach($products as $product){
            $price = new Price();
            $price->setProduct($product);
            $price->setAmount(10);
            $price->setCurrency("EUR");
            $manager->persist($price);
        }
        $manager->flush();
    }

}
