<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route("trick")]
class CommentController extends AbstractController
{


    #[Route("/edit/comment/{id}/", name: "edit.comment")]

    public function editComment(Comment $comment, Request $request, ManagerRegistry $doctrine)
    {

        $this->denyAccessUnlessGranted('edit_comment', $comment);

        $manager = $doctrine->getManager();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUpdatedAt(new \DateTimeImmutable());
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
    public function deleteComment(Comment $comment, ManagerRegistry $doctrine)
    {

        $this->denyAccessUnlessGranted('delete_comment', $comment);

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
