<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Security\AuthentificableRoles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{

    private $passwordEncoder;
    public function getDependencies()
    {
        return [
            CompanyFixtures::class
        ];
    }

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {

        $company = $this->getReference("company");

        $superAdmin = new User();
        $superAdmin->setEmail("superadmin@superadmin");
        $superAdmin->setFirstname("superadmin");
        $superAdmin->setLastname("superadmin");
        $superAdmin->setPassword($this->passwordEncoder->hashPassword($superAdmin, 'superadmin'));
        $superAdmin->setRoles([AuthentificableRoles::ROLE_SUPER_ADMIN]);
        $superAdmin->setActivate(true);
        $superAdmin->setCompany($company); // not logical but it's just for the demo 
        $manager->persist($superAdmin);


        $admin = new User();
        $admin->setEmail("admin@admin");
        $admin->setFirstname("admin");
        $admin->setLastname("admin");
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
        $admin->setRoles([AuthentificableRoles::ROLE_COMPANY_ADMIN]);
        $admin->setActivate(true);
        $admin->setCompany($company);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail("user@user");
        $user->setFirstname("user");
        $user->setLastname("user");
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'user'));
        $user->setRoles([AuthentificableRoles::ROLE_USER]);
        $user->setActivate(true);
        $user->setCompany($company);
        $manager->persist($user);

        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'test'));
            $user->setCompany($company); 
            $user->setActivate(true);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
