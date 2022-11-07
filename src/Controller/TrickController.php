<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/trick')]
class TrickController extends AbstractController
{

    #[
        Route('/add', name: 'add.trick'),
        IsGranted("ROLE_USER")
    
    ]
    public function addTrick(ManagerRegistry $doctrine, Request $request): Response
    {
       // $id = 18;
        //$repository = $entityManager->getRepository(User::class);
        //$user = $repository->findOneBy(['id' => $id]);
        $manager = $doctrine->getManager();
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('images') as $imageForm) {
                $imageFile = $imageForm->get('imageFile')->getData();
                $path = 'assets/figures/img/tricks/images_directory/';
                $fichier = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $fichier);
                $image = $imageForm->getData();
                $image->setPathImg($path . $fichier);
            }
            $trick->setSlug($trick->getName());
            $trick->setUser($this->getUser());
            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('success', 'la Figure <' . $trick->getName() . '> est ajouter avec succée');

            return $this->redirectToRoute('home');
        } else {

            return $this->render('trick/add-trick.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/{slug}', name: 'trick.details')]
    public function getTrick(TrickRepository $repo, Request $request, $slug, CommentRepository $commentRepository, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $trick = $repo->findOneBySlug($slug);

        $id = 18;

        $repository = $entityManager->getRepository(User::class);

        $user = $repository->findOneBy(['id' => $id]);

        $comments = $commentRepository->findBy(['trick' =>$trick->getId()], ['updatedAt' => 'DESC']);

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $manager = $doctrine->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUpdatedAt(new \DateTimeImmutable());
            $comment->setTrick($trick);
            $comment->setUser($user);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été enregistré !'
            );

            return $this->redirectToRoute('trick.details', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/details.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'comments' => $comments
        ]);
    }


    #[
        Route('/edit/{slug}', name: 'edit.trick'),
        IsGranted("ROLE_USER")
    ]
    public function editTrick(Request $request, TrickRepository $repo, ManagerRegistry $doctrine, $slug,  Filesystem $filesystem)
    {
        $trick = $repo->findOneBySlug($slug);

        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(TrickType::class, $trick);

        $manager = $doctrine->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('images') as $imageForm) {
                $imageFile = $imageForm->get('imageFile')->getData();
                if($imageFile){ 
                $path = 'assets/figures/img/tricks/images_directory/';
                $fichier = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $fichier);
                $image = $imageForm->getData();
                $image->setPathImg($path . $fichier);
                }
            }
            $trick->setUpdatedAt(new \DateTimeImmutable());
            $trick->setSlug($trick->getName());

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                'La figure <' . $trick->getName() . '>  a bien été modifié !'
            );

            return $this->redirectToRoute('trick.details', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit-trick.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }

    #[Route('/delete/{slug}', name: 'delete.trick')]
    public function deleteTrick(TrickRepository $repo, ManagerRegistry $doctrine, $slug)
    {
        $trick = $repo->findOneBySlug($slug);

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Vous devez être connecté pour acceder à cette page !');
        
        $manager = $doctrine->getManager();

        $fileSystem = new Filesystem();

        foreach ($trick->getImages() as $image) {
            $fileSystem->remove($image->getPathImg());
        }

        $manager->remove($trick);
        $manager->flush();

        $this->addflash(
            'danger',
            "Le trick <" . $trick->getName() . "> a été supprimé avec succès !"
        );

        return $this->redirectToRoute('home');
    }
}
