<?php

namespace App\Controller;

use App\Entity\Widget;
use App\Form\WidgetGraphType;
use App\Form\WidgetValuesType;
use App\Form\TypeWidgetType;
use App\Repository\WidgetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/widget")
 */
class WidgetController extends AbstractController
{
    /**
     * @Route("/", name="widget_index", methods={"GET"})
     */
    public function index(WidgetRepository $widgetRepository): Response
    {
        return $this->render('widget/index.html.twig', [
            'widgets' => $widgetRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{widgetType}", name="widget_new_typed", methods={"GET","POST"})
     */
    public function newTyped(Request $request, string $widgetType, WidgetRepository $widgetRepo): Response
    {
        
        $widgetForms = [
            'graph'     => WidgetGraphType::class,
            'values'    => WidgetValuesType::class
        ];

        // type existant ?
        if( !isset($widgetForms[$widgetType] ) ) {
            throw $this->createNotFoundException( "Le type de widget $widgetType n'existe pas" );
        }

        
        // new widget du type voulu
        $widget = new Widget();
        $widget->setWidgetType( $widgetType );
        
        // autocomplete
        if( $request->query->has('name') ) {
            $widget->setName( $request->query->get('name' ) );
        }
        
        // création du formaulaire du type de widget voulu
        $form = $this->createForm( $widgetForms[$widgetType], $widget);
        $form->handleRequest($request);

        // création du widget à la réception des données
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($widget);
            $entityManager->flush();

            return $this->redirectToRoute('widget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('widget/new.html.twig', [
            'widget' => $widget,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="widget_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $widget = new Widget();
        $form = $this->createForm(TypeWidgetType::class, $widget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeWidget = $widget->getWidgetType();

            return $this->redirectToRoute('widget_new_typed', ['widgetType' => $typeWidget, 'name' => $widget->getName()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('widget/new.html.twig', [
            'widget' => $widget,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="widget_show", methods={"GET"})
     */
    public function show(Widget $widget): Response
    {
        return $this->render('widget/show.html.twig', [
            'widget' => $widget,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="widget_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Widget $widget): Response
    {
        $widgetType = $widget->getWidgetType();

        // formualaires selon les types
        $widgetForms = [
            'graph'     => WidgetGraphType::class,
            'values'    => WidgetValuesType::class
        ];

        // type existant ?
        if( !isset($widgetForms[$widgetType] ) ) {
            $widgetType = 'graph'; // type par défaut en cas d'erreur ou de vieu widget sans type
        }

        $form = $this->createForm( $widgetForms[$widgetType], $widget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('widget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('widget/edit.html.twig', [
            'widget' => $widget,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="widget_delete", methods={"POST"})
     */
    public function delete(Request $request, Widget $widget): Response
    {
        if ($this->isCsrfTokenValid('delete'.$widget->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($widget);
            $entityManager->flush();
        }

        return $this->redirectToRoute('widget_index', [], Response::HTTP_SEE_OTHER);
    }
}
