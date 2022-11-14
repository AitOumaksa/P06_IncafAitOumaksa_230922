<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(TrickRepository $repo): Response
    {
        $tricks = $repo->findBy([], ['updatedAt' => 'DESC'] , 10, 0);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }
    
    #[Route('/{start}', name:'load.more', requirements:['start' => '\d+'])]
    public function loadMoreTricks(TrickRepository $repo, $start = 10)
    {
        // Get 15 tricks from the start position
        $tricks = $repo->findBy([], ['createdAt' => 'DESC'], 10, $start);

        return $this->render('home/load-more.html.twig', [
            'tricks' => $tricks
        ]);
    }
}
