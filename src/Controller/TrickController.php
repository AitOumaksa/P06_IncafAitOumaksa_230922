<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;

#[Route('/trick')]
class TrickController extends AbstractController
{
    #[Route('/{slug}', name: 'trick_details')]
    public function getTrick(TrickRepository $repo, $slug): Response
    {
        $trick = $repo->findOneBySlug($slug);

        return $this->render('trick/details.html.twig', [
            'trick' => $trick
        ]); 
    }
}
