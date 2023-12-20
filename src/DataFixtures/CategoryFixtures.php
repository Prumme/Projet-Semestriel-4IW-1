<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface{

    public function getDependencies() : array
    {
        return [
            CompanyFixtures::class,
            UserFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void{
        
        $faker = \Faker\Factory::create();
        for($i = 0; $i < 10; $i++){
            $category = new Category();
            $category->setName($faker->word);
            $category->setDescription($faker->text(100));
            $category->setCompany($this->getReference("company"));
            $manager->persist($category);
        }
        $manager->flush();
        
    }

}
