<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use App\Form\TrickType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;


#[Route('/trick')]
class TrickController extends AbstractController
{

    #[Route('/add', name: 'add.trick')]
    public function addTrick(ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = 18;
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $id]);
        $manager = $doctrine->getManager();
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('images') as $imageForm) {
                $imageFile = $imageForm->get('pathImg')->getData();
                $path = 'assets/figures/img/tricks/images_directory/';
                $fichier = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $fichier);
                $image = $imageForm->getData();
                $image->setPathImg($path . $fichier);
            }
            $trick->setSlug($trick->getName());
            $trick->setUser($user);
            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('success', 'la Figure ' . $trick->getName() . ' est ajouter avec succée');

            return $this->redirectToRoute('home');
        } else {

            return $this->render('trick/add-trick.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/{slug}', name: 'trick.details')]
    public function getTrick(TrickRepository $repo, $slug): Response
    {
        $trick = $repo->findOneBySlug($slug);

        return $this->render('trick/details.html.twig', [
            'trick' => $trick
        ]);
    }


    #[Route('/edit/{slug}', name: 'edit.trick')]
    public function editTrick(Request $request, TrickRepository $repo, ManagerRegistry $doctrine, $slug,  Filesystem $filesystem)
    {
        $trick = $repo->findOneBySlug($slug);

        $form = $this->createForm(TrickType::class, $trick);




        $manager = $doctrine->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($trick->getImages() as $oldImage)
                $oldImageFile = $oldImage->getPathImg();
            if ($oldImageFile) {
                $filesystem->remove($oldImageFile);
            }

            foreach ($form->get('images') as $imageForm) {
                $imageFile = $imageForm->get('pathImg')->getData();
                $path = 'assets/figures/img/tricks/images_directory/';
                $fichier = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $fichier);
                $image = $imageForm->getData();
                $image->setPathImg($path . $fichier);
            }
            $trick->setUpdatedAt(new \DateTimeImmutable());
            $trick->setSlug($trick->getName());

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                'La figure ' . $trick->getName() . '  a bien été modifié !'
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
}
