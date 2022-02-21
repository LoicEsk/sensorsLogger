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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\SensorDataRepository;


class SensorController extends AbstractController
{
    protected LoggerInterface $logger;

    public function __construct( LoggerInterface $logger )
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/api/sensor", name="api_sensor_get", methods={"GET"})
     */
    public function testGet( Request $request ): Response
    {
        $params = $request->query->all();
        $this->logger->info( "Requete GET reçue", $params );
        return $this->json( [
            'success' => true
        ]);
    }

    /**
     * @Route("/api/sensor", name="api_sensor_post", methods={"POST"})
     */
    public function testPost( Request $request, SensorRepository $sensorRepo ): Response
    {
        $sensorKeyCode = $request->request->get( 'sensor' ) ?: false;
        $sensorValue = (float) $request->request->get( 'value' ) ?: false;

        $em = $this->getDoctrine()->getManager();
        
        if( $sensorKeyCode && $sensorValue) {
            $this->logger->info( "Requete POST reçue", [ 'sensor' => $sensorKeyCode, 'value' => $sensorValue ] );

            // recherche du sensor
            $sensor = $sensorRepo->findOneByKeyCode( $sensorKeyCode );
            if( !$sensor ) {
                // sensor inconnu. Ajout en bdd en inactif
                $this->logger->debug( "sensor inconnu. Ajout en bdd en inactif" );
                $sensor = new Sensor( $sensorKeyCode );
                $em->persist( $sensor );
            } else {
                $this->logger->debug( "Sensor $sensorKeyCode reconnu" );
                if( $sensor->getActif() ) {
                    $this->logger->debug('Sensor actif');
                    // sensor actif -> on enregistre la valeur
                    $data = new SensorData( $sensor, $sensorValue );
                    $em->persist( $data );
                }
            }
        }

        $em->flush();

        return $this->json( [
            'success' => true
        ]);
    }

    /**
     * @Route("/api/sensor/{id}", name="api_sensor_show", methods={"GET"})
     * @isGranted("ROLE_USER")
     */
    public function show( Sensor $sensor )
    {
        return $this->json( $sensor );
    } 

    /**
     * @Route("/api/sensor/{id}/data", name="api_sensorData_show", methods={"GET"})
     * @isGranted("ROLE_USER")
     */
    public function sensorData( Request $request, Sensor $sensor, SensorDataRepository $sensorDataRepo )
    {
        $from = $request->query->has('from') ? new \DateTime( $request->query->get('from') ) : new \DateTime();
        $to = $request->query->has('to') ? new \DateTime( $request->query->get('to') ) : new \DateTime();

        $this->logger->debug( "Lecture des data du sensor #{$sensor->getId()} de {$from->format('Y-m-d H:i:m')} à {$to->format('Y-m-d H:i:m')}" );
        
        $datas = $sensorDataRepo->findBetween( $sensor, $from, $to );
        return $this->json( $datas );
    } 
}
