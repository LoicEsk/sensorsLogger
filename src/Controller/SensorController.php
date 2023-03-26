<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Form\SensorType;
use App\Repository\SensorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SensorDataRepository;
use App\Entity\SensorData;

/**
 * @Route("/sensor")
 */
class SensorController extends AbstractController
{
    /**
     * @Route("/", name="sensor_index", methods={"GET"})
     */
    public function index(SensorRepository $sensorRepository): Response
    {
        return $this->render('sensor/index.html.twig', [
            'sensors' => $sensorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sensor_new", methods={"GET","POST"}, schemes={"https", "http"})
     */
    public function new(Request $request): Response
    {
        $sensor = new Sensor();
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sensor);
            $entityManager->flush();

            return $this->redirectToRoute('sensor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sensor/new.html.twig', [
            'sensor' => $sensor,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sensor_show", methods={"GET"})
     */
    public function show(Sensor $sensor, SensorDataRepository $sensorDataRepo ): Response
    {
        $sensorData = $sensorDataRepo->findBy( [ 'sensor' => $sensor ], [ 'date' => 'DESC'], 1000 );

        return $this->render('sensor/show.html.twig', [
            'sensor' => $sensor,
            'data'      => $sensorData
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sensor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sensor $sensor): Response
    {
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sensor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sensor/edit.html.twig', [
            'sensor' => $sensor,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sensor_delete", methods={"POST"})
     */
    public function delete(Request $request, Sensor $sensor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sensor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            // suppression des données liées au capteur
            $entityManager->getRepository( SensorData::class )->deleteSendorData( $sensor );
            $entityManager->flush();

            // suppression du sensor
            $entityManager->remove($sensor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sensor_index', [], Response::HTTP_SEE_OTHER);
    }
}
