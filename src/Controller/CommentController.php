<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/trick/delete/comment/{id}', name: 'delete.comment')]
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
