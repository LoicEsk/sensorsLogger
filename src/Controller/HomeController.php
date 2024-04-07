<?php

namespace App\Controller;

use App\Repository\WidgetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index( WidgetRepository $widgetRepo ): Response
    {
        $widgets = $widgetRepo->findBy( [], [ 'position' => 'ASC' ]);

        return $this->render('home/index.html.twig', [
            'widgets' => $widgets,
        ]);
    }
}
