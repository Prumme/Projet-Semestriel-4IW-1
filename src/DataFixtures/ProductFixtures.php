<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Company;
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

    public function createProducts(Company $company, ObjectManager $manager){
        $faker = \Faker\Factory::create();
        $categories = $manager->getRepository(Category::class)->findAll();
        $users = $manager->getRepository(User::class)->findBy(["company" => $this->getReference("company")]);

        for($i = 0; $i < 10; $i++){
            $product = new Product();
            $product->setName($faker->word);
            $product->setDescription($faker->text(100));
            $product->setPrice($faker->randomFloat(2, 0, 100));
            $product->setCompany($company);
            $product->addCategory($categories[rand(0, count($categories) - 1)]);
            $product->addCategory($categories[rand(0, count($categories) - 1)]);
            $product->setUserId($users[rand(0, count($users) - 1)]);
            $manager->persist($product);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager): void{
        $company = $this->getReference("company");
        $company2 = $this->getReference("company2");

        $this->createProducts($company, $manager);
        $this->createProducts($company2, $manager);

    }

}
