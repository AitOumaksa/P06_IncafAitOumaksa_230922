<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(TricksRepository $repo): Response
    {
        $tricks = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }


}
