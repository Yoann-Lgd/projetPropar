<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Entity\StatutOperation;
use App\Form\OperationType;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

/**
 * @Route("/operation")
 */
class OperationController extends AbstractController
{
    /**
     * @Route("/", name="operation_index", methods={"GET", "POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager,OperationRepository $operationRepository): Response
    {
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);
        // $nbOp = $operationRepository->getRepository(Operation::class);
        // $nbOp->countByUserId($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($operation->getUtilisateur()) ) {
                $statutOperation = $entityManager->getRepository(StatutOperation::class)
                                                ->findOneBy(['id' => 2]);
                $operation->setStatutOperation($statutOperation);
                $entityManager->persist($operation);
                $entityManager->flush();
            } else {
                $statutOperation = $entityManager->getRepository(StatutOperation::class) 
                                                    ->findOneBy(['id' => 1]);
                $operation->setStatutOperation($statutOperation);
                $entityManager->persist($operation);
                $entityManager->flush();
            }
            
            $this->addFlash('success', 'Votre opération a été crée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('operation/index.html.twig', [
            'operations' => $operationRepository->findAll(),
            'operation' => $operation,
            'form' => $form,
        ]);
    }

    // /**
    //  * @Route("/new", name="operation_new", methods={"GET", "POST"})
    //  */
    // public function new(Request $request, EntityManagerInterface $entityManager,OperationRepository $operationRepository): Response
    // {
    //     $operation = new Operation();
    //     $form = $this->createForm(OperationType::class, $operation);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         if (!empty($operation->getUtilisateur()) ) {
    //             $statutOperation = $entityManager->getRepository(StatutOperation::class)
    //                                             ->findOneBy(['id' => 2]);
    //             $operation->setStatutOperation($statutOperation);
    //             $entityManager->persist($operation);
    //             $entityManager->flush();
    //         } else {
    //             $statutOperation = $entityManager->getRepository(StatutOperation::class) 
    //                                                 ->findOneBy(['id' => 1]);
    //             $operation->setStatutOperation($statutOperation);
    //             $entityManager->persist($operation);
    //             $entityManager->flush();
    //         }
            
    //         $this->addFlash('success', 'Votre opération a été crée.');
    //         return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('operation/new.html.twig', [
    //         'operations' => $operationRepository->findAll(),
    //         'operation' => $operation,
    //         'form' => $form,
    //     ]);
    // }

    /**
     * @Route("/{id}", name="operation_show", methods={"GET"})
     */
    public function show(Operation $operation): Response
    {
        $form = $this->createForm(OperationType::class, $operation);
        return $this->render('operation/new.html.twig', [
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
     * @Route("/{id}/delete", name="operation_delete", methods={"POST"})
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
     * @Route("/{id}/reserved", name="operation_reserved", methods={"POST"})
     */
    public function reserved(Request $request, Operation $operation, EntityManagerInterface $entityManager, OperationRepository $operationRepository): Response
    {
        $user = $this->getUser();
        $roles = implode($this->getUser()->getRoles());
        if ($roles == "ROLE_EXPERT") {
            $roles = 1;
        }elseif ($roles == "ROLE_SENIOR") {
            $roles = 2;
        }elseif ($roles == "ROLE_APPRENTI") {
            $roles = 3;
        }

        $statutOperation = $entityManager->getRepository(StatutOperation::class) 
                                        ->findOneBy(['id' => 2]); 
        $nbOp = $operationRepository->getRepository(Operation::class);
        $nbOp->countByUserId($user)->findAll();
        // $form = $this->createForm(OperationType::class, $operation);
        if ($this->isCsrfTokenValid('reserved'.$operation->getId(), $request->request->get('_token'))  && (($roles==1 && $nbOp<6) || ($roles==2 && $nbOp<4) || ($roles==3 && $nbOp<2)) ){
            // $form->handleRequest($request);
            $operation->setUtilisateur($user); 
            $operation->setStatutOperation($statutOperation); 
            $entityManager->persist($operation); 
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été réservée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
            
        }
        $this->addFlash('error', 'Votre opération n\'a pas été réservée.');
        return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/finish", name="operation_finish", methods={"POST"})
     */
    public function finish(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        $statutOperation = $entityManager->getRepository(StatutOperation::class)
                                        ->findOneBy(['id' => 3]); 
        if ($this->isCsrfTokenValid('finish'.$operation->getId(), $request->request->get('_token'))) {
            $operation->setStatutOperation($statutOperation); 
            $entityManager->persist($operation);
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été terminée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
            
        }
        $this->addFlash('error', 'Votre opération n\'a pas été terminée.');
        return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
    }
    
}