<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface{

    public function getDependencies() : array
    {
        return [
            CompanyFixtures::class,
            UserFixtures::class,
            CategoryFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void{
        
        $faker = \Faker\Factory::create();
        $categories = $manager->getRepository(Category::class)->findAll();
        $users = $manager->getRepository(User::class)->findBy(["company" => $this->getReference("company")]);

        for($i = 0; $i < 10; $i++){
            $product = new Product();
            $product->setName($faker->name);
            $product->setDescription($faker->text);
            $product->setCompany($this->getReference("company"));
            $product->addCategory($categories[rand(0, count($categories) - 1)]);
            $product->addCategory($categories[rand(0, count($categories) - 1)]);
            $product->setUserId($users[rand(0, count($users) - 1)]);
            $manager->persist($product);
        }
        $manager->flush();
    }

}
