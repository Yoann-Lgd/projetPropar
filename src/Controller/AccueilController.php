<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Client;
use App\Entity\Operation;
use App\Security\EmailVerifier;
use App\Repository\ClientRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\OperationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ClientType;
use App\Form\RegistrationFormType;
use App\Form\UpdateUserFormType;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class AccueilController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ClientRepository $clientRepository, UtilisateurRepository $utilisateurRepository, OperationRepository $operationRepository): Response
    {
        $user = new Utilisateur();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $registrationForm->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('projet.propar@gmail.com', 'Propar-noreply'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            // $this->addFlash('success', 'Votre adresse e-mail a été vérifiée.');
            return $this->redirectToRoute('accueil');
        }

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('accueil/index.html.twig', [
            'clients' => $clientRepository->findAll(),
            'utilisateurs' => $utilisateurRepository->findAll(),
            'operations' => $operationRepository->findAll(),
            'form' => $form->createView(),
            'registrationForm' => $registrationForm->createView(),
        ]);
    }

   /**
     * @Route("/{id}/delete", name="utilisateur_delete", methods={"POST"})
     */
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $op = $entityManager->getRepository(Operation::class)
                                        ->findOneBy(['utilisateur' => $utilisateur]);
            if ($op == NULL) {
                $entityManager->remove($utilisateur);
                $entityManager->flush();
                $this->addFlash('success', 'L\'utilisateur a été suprimé.');
                return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);

            } else {
                $this->addFlash('error', 'L\'utilisateur ne peut pas être supprimé car il existe des opérations qui lui sont affectées.');
                return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);
                
            }
           
        }
        $this->addFlash('error', 'L\'utilisateur n\'a pas été suprimé.');
        return $this->redirectToRoute($request->request->get('_url'), [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/update", name="utilisateur_edit", methods={"GET", "POST"})
     */
    public function register(Utilisateur $utilisateur,Request $request, EntityManagerInterface $entityManager): Response
    {
        $formRegister = $this->createForm(UpdateUserFormType::class, $utilisateur);
        $formRegister->handleRequest($request);

        if ($formRegister->isSubmitted() && $formRegister->isValid()) {
            // encode the plain password
            
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // $this->addFlash('success', 'Votre adresse e-mail a été vérifiée.');
            return $this->redirectToRoute('accueil');
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'registrationForm' => $formRegister,
        ]);
    }


    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('accueil');
    }

}
