<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: "main_home", methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    #[Route('/about_us', name: "main_about_us", methods: ['GET'])]
    public function aboutUs(): Response
    {
        return $this->render('main/about-us.html.twig');
    }
}
