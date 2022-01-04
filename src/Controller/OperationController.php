<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Form\OperationType;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/operation")
 */
class OperationController extends AbstractController
{
    /**
     * @Route("/", name="operation_index", methods={"GET"})
     */
    public function index(OperationRepository $operationRepository): Response
    {
        return $this->render('operation/index.html.twig', [
            'operations' => $operationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="operation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($operation);
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été crée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('operation/new.html.twig', [
            'operation' => $operation,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="operation_show", methods={"GET"})
     */
    public function show(Operation $operation): Response
    {
        $form = $this->createForm(OperationType::class, $operation);
        return $this->render('operation/show.html.twig', [
            'operation' => $operation,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="operation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été modifiée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
        } 
        return $this->renderForm('operation/edit.html.twig', [
            'operation' => $operation,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="operation_delete", methods={"POST"})
     */
    public function delete(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$operation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($operation);
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été suprimée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
            
        }
        $this->addFlash('error', 'Votre opération n\'a pas été suprimée.');
        return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
    }
    
    /**
     * @Route("/{id}", name="operation_reserved", methods={"POST"})
     */
    public function reserved(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OperationType::class, $operation);
        if ($this->isCsrfTokenValid('reserved'.$operation->getId(), $request->request->get('_token'))) {
            $form->handleRequest($request);
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été réservée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
            
        }
        $this->addFlash('error', 'Votre opération n\'a pas été réservée.');
        return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
    }
    
}