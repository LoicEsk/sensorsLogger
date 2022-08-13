<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\WidgetRepository;

/**
 * @Route("/api/widget")
 */
class WidgetController extends AbstractController
{
    /**
     * @Route("/", name="api_widget_index", methods={"GET"})
     */
    public function index(WidgetRepository $widgetRepository): Response
    {
        $widgets = $widgetRepository->findBy( [], [ 'position' => 'ASC' ] );
        return $this->json( $widgets );
    }
}
