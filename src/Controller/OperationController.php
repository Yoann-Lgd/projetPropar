<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Entity\StatutOperation;
use App\Form\OperationType;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\GrantedService;
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
    public function index(GrantedService $grantedService,Request $request, EntityManagerInterface $entityManager,OperationRepository $operationRepository): Response
    {
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);

        $nombreOperation = $operationRepository->countByUserID($this->getUser());
        if ($grantedService->isGranted($this->getUser(), 'ROLE_EXPERT') ) $maxOperation = 5;
        elseif ($grantedService->isGranted($this->getUser(), 'ROLE_SENIOR') ) $maxOperation = 3;
        else $maxOperation = 1;

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($operation->getUtilisateur()) ) {
                $nombreOperation = $operationRepository->countByUserID($operation->getUtilisateur());
                if ($grantedService->isGranted($operation->getUtilisateur(), 'ROLE_EXPERT') ) $maxOperation = 5;
                elseif ($grantedService->isGranted($operation->getUtilisateur(), 'ROLE_SENIOR') ) $maxOperation = 3;
                else $maxOperation = 1;

                if ($nombreOperation[1] < $maxOperation) {
                    $statutOperation = $entityManager->getRepository(StatutOperation::class)
                                                     ->findOneBy(['id' => 2]);
                    $operation->setStatutOperation($statutOperation);
                    $entityManager->persist($operation);
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre opération a bien été crée et affecté à '.$operation->getUtilisateur());
                } else {
                    $this->addFlash('error', 'Votre opération n\'a pas été crée car l\'utilisateur  '.$operation->getUtilisateur().' a atteint son cota. Veuillez sélectionner un autre utilisateur');
                    $statutOperation = $entityManager->getRepository(StatutOperation::class) 
                                                     ->findOneBy(['id' => 1]);
                    $operation->setUtilisateur(NULL);
                    $operation->setStatutOperation($statutOperation);
                    $entityManager->persist($operation);
                    $entityManager->flush();
                    }
            } else {
                $statutOperation = $entityManager->getRepository(StatutOperation::class) 
                                                 ->findOneBy(['id' => 1]);
                $operation->setStatutOperation($statutOperation);
                $entityManager->persist($operation);
                $entityManager->flush();
                $this->addFlash('success', 'Votre opération a été crée.');
            }
        }
        return $this->render('operation/index.html.twig', [
            'operations' => $operationRepository->findAll(),
            'operation' => $operation,
            'form' => $form->createView(),
            'nombreOperation' => $nombreOperation[1],
            'maxOperation' => $maxOperation,
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
        $form = $this->createForm(OperationType::class, $operation, [
            'action' => $this->generateUrl('operation_edit', ['id' => $operation->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été modifiée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
        } 
        return $this->render('operation/edit.html.twig', [
            'operation' => $operation,
            'form' => $form->createView(),
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
    public function reserved(GrantedService $grantedService, Request $request, Operation $operation, EntityManagerInterface $entityManager, OperationRepository $operationRepository): Response
    {
        $user = $this->getUser();
       
        $statutOperation = $entityManager->getRepository(StatutOperation::class)
                                         ->findOneBy(['id' => 2]);

        $nombreOperation = $operationRepository->countByUserID($this->getUser());

        $boolOperation = false;
        if ($grantedService->isGranted($user, 'ROLE_EXPERT') &&  $nombreOperation[1] < 5) $boolOperation = true;
        elseif ($grantedService->isGranted($user, 'ROLE_SENIOR') &&  $nombreOperation[1] < 3) $boolOperation = true;
        elseif ($grantedService->isGranted($user, 'ROLE_APPRENTI') &&  $nombreOperation[1] < 1) $boolOperation = true;


        if ($this->isCsrfTokenValid('reserved'.$operation->getId(), $request->request->get('_token')) && $boolOperation ) {
            
            $operation->setUtilisateur($user); 
            $operation->setStatutOperation($statutOperation); 
            $entityManager->persist($operation);
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été réservée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
            
        }
        $this->addFlash('error', 'Votre opération n\'a pas été réservée, vous avez déjà '.$nombreOperation[1].' opérations en cours.');
        return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
    }

        /**
     * @Route("/{id}/release", name="operation_release", methods={"POST"})
     */
    public function release (GrantedService $grantedService, Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {

        $statutOperation = $entityManager->getRepository(StatutOperation::class)
                                         ->findOneBy(['id' => 1]);
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('reserved'.$operation->getId(), $request->request->get('_token')) && $grantedService->isGranted($user, 'ROLE_EXPERT') ) {
            
            $operation->setUtilisateur(NULL); 
            $operation->setStatutOperation($statutOperation); 
            $entityManager->persist($operation);
            $entityManager->flush();
            $this->addFlash('success', 'Votre opération a été libérée.');
            return $this->redirectToRoute('operation_index', [], Response::HTTP_SEE_OTHER);
            
        }
        $this->addFlash('error', 'Votre opération n\'a pas libérée');
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