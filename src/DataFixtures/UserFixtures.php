<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
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
        $users = [];
        $genders = ['male', 'female'];
        $gender = $faker->randomElement($genders);
        $date  = new \DateTimeImmutable();
        $category = ['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school'];
        $tricksName = ['Mute', 'Indy', '360', '720', 'Backflip', 'Misty', 'Tail slide', 'Method air', 'Backside air'];

        //Admin
        $admin = new User();
        $admin->setUserName('Incaf Ait Oumaksa');
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setCreatedAt($date);
        $admin->setUpdatedAt($date);
        $manager->persist($admin);

        //User
        for ($i=1; $i<=3;$i++) 
        {
            $user = new User();
            
            $user->setUserName($faker->name);
            $user->setEmail("user$i@gmail.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'admin'));
            $user->setAvatar('https://randomuser.me/api/portraits/' . ($gender == 'male' ? 'men/' : 'women/') . $faker->numberBetween(1,99) . '.jpg');
            $user->setCreatedAt($date);
            $user->setUpdatedAt($date);
            $users[] = $user;
            $manager->persist($user);
          
        }
        //Génerate tricks 
        foreach ($tricksName as $trickName)
        {
           
            $trick = new Trick();
            $trick->setName($trickName);
            $trick->setDescription($faker->paragraph( 5, true));
            
            $trick->setCreatedAt($date);
            $trick->setUpdatedAt($date);
            $trick->setSlug($trickName);
            $trick->setCatégory($faker->randomElement($category));
            $trick->setUser($faker->randomElement($users));
            $manager->persist($trick);

            for ($k=1; $k<4; $k++)
            {
                $image = new Image();
                $image->setPathImg('img/tricks/' . $trick->getName() . ' ' . $k . '.jpg');
                $image->setTrick($trick);
                $manager->persist($image);
            }
          

            // 1 to 2 Video by Trick
            for ($l=0; $l<mt_rand(1, 2); $l++)
            {
            $video = new Video();
            $video->setUrlVideo('https://www.youtube.com/embed/tHHxTHZwFUw');
            $video->setTrick($trick);      
            $manager->persist($video);
            }
            // 0 to 30 Comment by Trick
            for ($m=0; $m<mt_rand(0, 30); $m++)
            {
                $comment = new Comment();
                $comment->setContent($faker->sentence(mt_rand(1, 5)));
                $comment->setCreatedAt($date);
                $comment->setUpdatedAt($date);
               $comment->setUser($faker->randomElement($users));
               $comment->setTrick($trick);
                
                $manager->persist($comment);
            } 

        }

        
        $manager->flush();
    }
 
}
