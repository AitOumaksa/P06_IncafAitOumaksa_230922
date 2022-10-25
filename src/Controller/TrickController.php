<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\TrickType;
use App\Form\VideosType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


#[Route('/trick')]
class TrickController extends AbstractController
{

    public function getUser(): User {
        if($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                return $user;
            }
        }
    }
  
    #[Route('/add', name: 'add.trick')]
    public function addTrick(ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = 18 ;
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $id]);
        $manager = $doctrine->getManager();
        $trick = new Trick();
        $Videos = new Video();
        $form = $this->createForm(TrickType::class,$trick);  
        $form->remove('user');
        $form->remove('slug');
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $path='assets/figures/img/tricks/images_directory/';
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move($this->getParameter('images_directory'), $fichier);
                $img = new Image();
                $img->setPathImg($path.$fichier);
                $trick->addImage($img);
            }

            foreach($trick->getVideos() as $video)
            {
                $video->setTrick($trick);
                $manager->persist($video);
            }

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());
            $trick->setSlug($trick->getName());
            $trick->setUser($user);

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('success' , 'la Figure '.$trick->getName().' est ajouter avec succée');

            return $this->redirectToRoute('home');
        }else{

            return $this->render('trick/add-trick.html.twig', [
                'form' => $form->createView()
            ]); 
        }

  
    }

    #[Route('/{slug}', name: 'trick_details')]
    public function getTrick(TrickRepository $repo, $slug): Response
    {
        $trick = $repo->findOneBySlug($slug);

        return $this->render('trick/details.html.twig', [
            'trick' => $trick
        ]); 
    }
}
