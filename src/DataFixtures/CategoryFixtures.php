<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Company;
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

    private function createCategory(Company $company, ObjectManager $manager){
        $faker = \Faker\Factory::create();
        for($i = 0; $i < 10; $i++){
            $category = new Category();
            $category->setName($faker->word);
            $category->setDescription($faker->text(100));
            $category->setCompany($company);
            $manager->persist($category);
        }
        $manager->flush();
    }
    public function load(ObjectManager $manager): void{
        $company = $this->getReference("company");
        $company2 = $this->getReference("company2");

        $this->createCategory($company, $manager);
        $this->createCategory($company2, $manager);
    }

}
