<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\ClientRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(ClientRepository $clientRepository, UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('accueil/index.html.twig', [
            'clients' => $clientRepository->findAll(),
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="utilisateur_delete", methods={"POST"})
     */
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été suprimé.');
            return $this->redirectToRoute('accueil_index', [], Response::HTTP_SEE_OTHER);
            
        }
        $this->addFlash('error', 'L\'utilisateur n\'a pas été suprimé.');
        return $this->redirectToRoute('accueil_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('accueil');
    }
}
