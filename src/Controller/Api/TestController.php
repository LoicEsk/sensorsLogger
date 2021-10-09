<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class TestController extends AbstractController
{
    /**
     * @Route("/api/test", name="api_test_get", methods={"GET"})
     */
    public function testGet( Request $request, LoggerInterface $logger ): Response
    {
        $params = $request->query->all();
        $logger->info( "Requete GET reçue", $params );
        return $this->json( [
            'success' => true
        ]);
    }

    /**
     * @Route("/api/test", name="api_test_post", methods={"POST"})
     */
    public function testPost( Request $request, LoggerInterface $logger ): Response
    {
        $params = $request->request->all();
        $logger->info( "Requete POST reçue", $params );
        return $this->json( [
            'success' => true
        ]);
    }
}
