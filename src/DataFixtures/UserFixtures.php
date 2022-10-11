<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserFixtures extends Fixture 
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
  

    public function load(ObjectManager $manager): void
    {
      
        // $product = new Product();
        // $manager->persist($product);

        $faker =Factory::create('fr_FR');
        $genders = ['male', 'female'];
        $gender = $faker->randomElement($genders);
        $date  = new \DateTimeImmutable();
        $admin = new User();
        $admin->setUserName('Incaf Ait Oumaksa');
        $admin->setEmail('admin@gmail.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setIsAdmin(1);
        $admin->setCreatedAt($date);
        $admin->setUpdatedAt($date);
        $manager->persist($admin);
        
        for ($i=1; $i<=3;$i++) {
            $user = new User();
            
            $user->setUserName($faker->name);
            $user->setEmail("user$i@gmail.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'admin'));
            $user->setAvatar('https://randomuser.me/api/portraits/' . ($gender == 'male' ? 'men/' : 'women/') . $faker->numberBetween(1,99) . '.jpg');
            $user->setIsAdmin(0);
            $user->setCreatedAt($date);
            $user->setUpdatedAt($date);
            $manager->persist($user);
        }
        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['user'];
    }
}
