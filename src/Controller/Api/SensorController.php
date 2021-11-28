<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Entity\Sensor;
use App\Repository\SensorRepository;
use App\Entity\SensorData;

class SensorController extends AbstractController
{
    /**
     * @Route("/api/sensor", name="api_sensor_get", methods={"GET"})
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
     * @Route("/api/sensor", name="api_sensor_post", methods={"POST"})
     */
    public function testPost( Request $request, LoggerInterface $logger, SensorRepository $sensorRepo ): Response
    {
        $sensorKeyCode = $request->request->get( 'sensor' ) ?: false;
        $sensorValue = (float) $request->request->get( 'value' ) ?: false;

        $em = $this->getDoctrine()->getManager();
        $logger->info( "Requete POST reçue", [ 'sensor' => $sensorKeyCode, 'value' => $sensorValue ] );

        if( !!$sensorKeyCode && !!$sensorValue) {

            // recherche du sensor
            $sensor = $sensorRepo->findOneByKeyCode( $sensorKeyCode );
            if( !$sensor ) {
                // sensor inconnu. Ajout en bdd en inactif
                $sensor = new Sensor( $sensorKeyCode );
                $em->persist( $sensor );
            } else {
                if( $sensor->getActif() ) {
                    // sensor actif -> on enregistre la valeur
                    $data = new SansorData( $sensor, $sensorValue );
                    $em->persist( $data );
                }
            }
        }

        $em->flush();

        return $this->json( [
            'success' => true
        ]);
    }
}
