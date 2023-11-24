<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {

        //create admin user
        $admin = new User();
        $admin->setEmail("admin@admin");
        $admin->setFirstname("admin");
        $admin->setLastname("admin");
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail("user@user");
        $user->setFirstname("user");
        $user->setLastname("user");
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'user'));
        $manager->persist($user);

        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'test'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
