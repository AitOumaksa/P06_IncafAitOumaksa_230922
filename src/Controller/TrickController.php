<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/trick')]
class TrickController extends AbstractController
{

    #[Route('/add', name: 'add.trick')]
    public function addTrick(ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
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
        }

        return $this->render('trick/add-trick.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{slug}', name: 'trick.details')]
    public function getTrick(Request $request, CommentRepository $commentRepository, ManagerRegistry $doctrine, Trick $trick): Response
    {

        $comments = $commentRepository->findBy(['trick' => $trick->getId()], ['updatedAt' => 'DESC'], 10, 0);

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $manager = $doctrine->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

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

    #[Route("/{slug}/{start}", name: 'loadMore.comments', requirements: ["start" => "\d+"])]
    public function loadMoreComments(Trick $trick, CommentRepository $commentRepository, $start = 10)
    {
        $comments = $commentRepository->findBy(['trick' => $trick->getId()], ['updatedAt' => 'DESC'], 10, $start);

        return $this->render('trick/load-more-comments.html.twig', [
            'trick' => $trick,
            'start' => $start,
            'comments' => $comments
        ]);
    }

    #[Route('/edit/{slug}', name: 'edit.trick')]
    public function editTrick(Request $request, ManagerRegistry $doctrine, Trick $trick)
    {

        $this->denyAccessUnlessGranted('edit_trick', $trick);

        $form = $this->createForm(TrickType::class, $trick);

        $manager = $doctrine->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('images') as $imageForm) {
                $imageFile = $imageForm->get('imageFile')->getData();
                if ($imageFile) {
                    $path = 'assets/figures/img/tricks/images_directory/';
                    $fichier = md5(uniqid()) . '.' . $imageFile->guessExtension();
                    $imageFile->move($this->getParameter('images_directory'), $fichier);
                    $image = $imageForm->getData();
                    $image->setPathImg($path . $fichier);
                }
            }
            $trick->setUpdatedAt(new \DateTimeImmutable());
            $trick->setSlug($trick->getName());

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
    public function deleteTrick(Trick $trick, ManagerRegistry $doctrine, $slug)
    {
        $this->denyAccessUnlessGranted('delete_trick', $trick);

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
