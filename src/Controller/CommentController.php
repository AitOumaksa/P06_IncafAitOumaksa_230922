<?php

namespace App\Controller;


use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route("trick")]
class CommentController extends AbstractController
{

    
    #[Route("/edit/comment/{id}/", name : "edit.comment")]
   
    public function edit(CommentRepository $repo, Request $request , $id ,ManagerRegistry $doctrine)
    {
       
        $comment = $repo->findOneBy(['id' => $id]);

        $manager = $doctrine->getManager();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été modifié !'
            );

            return $this->redirectToRoute('trick.details', array('slug' => $comment->getTrick()->getSlug()));
        }

        return $this->render('comment/edit-comment.html.twig', [
            'comment' => $comment,
            'form' => $form,
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/comment/{id}', name: 'delete.comment')]
    public function deleteComment(CommentRepository $repo, ManagerRegistry $doctrine, $id)
    {  

        $comment = $repo->findOneBy(['id' => $id]);

        $manager = $doctrine->getManager();
        
        $manager->remove($comment);

        $manager->flush();

        $this->addFlash(
            'danger',
            'Votre commentaire a bien été supprimer !'
        );

        return $this->redirectToRoute('trick.details', ['slug' => $comment->getTrick()->getSlug()]);
    }
}
